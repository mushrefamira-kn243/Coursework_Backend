<?php
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $path = __DIR__ . '/../../db/database.sqlite';
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->pdo = new PDO('sqlite:' . $path);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('PRAGMA foreign_keys = ON');

        $this->initialize();
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    private function initialize()
    {
         
        $sqlFile = __DIR__ . '/../../db/database.sql';
        $hasTables = false;
        try {
            $res = $this->pdo->query("SELECT name FROM sqlite_master WHERE type='table' LIMIT 1");
            if ($res && $res->fetch()) {
                $hasTables = true;
            }
        } catch (Exception $e) {
            $hasTables = false;
        }

        if (!$hasTables && file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
             
            $this->pdo->exec($sql);
        }

         
        $this->migrateTableColumns('products', [
            'image' => "TEXT DEFAULT ''"
        ]);
        $this->migrateTableColumns('news', [
            'image' => 'TEXT'
        ]);
        $this->migrateTableColumns('users', [
            'login' => "TEXT DEFAULT ''",
            'password' => "TEXT DEFAULT ''",
            'avatar' => "TEXT DEFAULT ''"
        ]);
        $this->migrateTableColumns('orders', [
            'status' => "TEXT DEFAULT 'in_process'"
        ]);

        $this->normalizeUserRoles();
        $this->ensureDefaultUsers();
        $this->seedSampleData();
        $this->syncJsonFilesFromDatabase();
    }

    private function syncJsonFilesFromDatabase()
    {
        $tables = ['products', 'news', 'users', 'gallery', 'pages'];
        $dataDir = __DIR__ . '/../../data';
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
        }

        foreach ($tables as $table) {
            $stmt = $this->pdo->query('SELECT * FROM ' . $table . ' ORDER BY id ASC');
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($table === 'users') {
                foreach ($items as &$item) {
                    unset($item['password']);
                }
                unset($item);
            }
            file_put_contents($dataDir . '/' . $table . '.json', json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    private function normalizeUserRoles()
    {
        $this->pdo->exec("UPDATE users SET role = 'user' WHERE role NOT IN ('admin', 'user') OR role IS NULL OR role = ''");
    }

    private function ensureDefaultUsers()
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE login = :login');
        $stmt->execute(['login' => 'admin']);
        if (intval($stmt->fetchColumn()) === 0) {
            $this->addDefaultUser('admin', 'Адміністратор', 'admin@example.com', 'music123', 'admin', '2026-01-01');
        }

        $stmt->execute(['login' => 'user']);
        if (intval($stmt->fetchColumn()) === 0) {
            $this->addDefaultUser('user', 'Користувач', 'user@example.com', 'user123', 'user', '2026-01-15');
        }
    }

    private function addDefaultUser(string $login, string $name, string $email, string $password, string $role, string $registered): void
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO users (login, name, email, password, role, registered) VALUES (:login, :name, :email, :password, :role, :registered)');
        $stmt->execute([
            'login' => $login,
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
            'role' => $role,
            'registered' => $registered
        ]);
    }

    private function migrateTableColumns(string $table, array $columns)
    {
        $stmt = $this->pdo->query("PRAGMA table_info('{$table}')");
        $existing = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'name');

        foreach ($columns as $name => $definition) {
            if (!in_array($name, $existing, true)) {
                $this->pdo->exec("ALTER TABLE {$table} ADD COLUMN {$name} {$definition}");
            }
        }
    }

    private function seedSampleData()
    {
        $defaultProducts = [
            ['name' => 'Акустична гітара Yamaha F310', 'category' => 'Гітари', 'price' => 4999.00, 'description' => 'Універсальна акустична гітара для початківців.', 'stock' => 8, 'image' => 'https://via.placeholder.com/520x320?text=Yamaha+F310'],
            ['name' => 'Електрогітара Fender Stratocaster', 'category' => 'Гітари', 'price' => 18999.00, 'description' => 'Класичний звук та зручний гриф.', 'stock' => 4, 'image' => 'https://via.placeholder.com/520x320?text=Fender+Stratocaster'],
            ['name' => 'Бас-гітара Ibanez GSR200', 'category' => 'Гітари', 'price' => 14999.00, 'description' => 'Легка та збалансована бас-гітара для ритму та соло.', 'stock' => 5, 'image' => 'https://via.placeholder.com/520x320?text=Ibanez+GSR200'],
            ['name' => 'Класична гітара Cordoba C3M', 'category' => 'Гітари', 'price' => 7999.00, 'description' => 'Комфортний гриф та теплий звук для класичної музики.', 'stock' => 7, 'image' => 'https://via.placeholder.com/520x320?text=Cordoba+C3M'],
            ['name' => 'Укулеле Kala KA-15S', 'category' => 'Гітари', 'price' => 2599.00, 'description' => 'Компактний інструмент для подорожей та навчання.', 'stock' => 12, 'image' => 'https://via.placeholder.com/520x320?text=Kala+KA-15S'],
            ['name' => 'Клавішний синтезатор Korg', 'category' => 'Клавішні інструменти', 'price' => 12999.00, 'description' => 'Портативний синтезатор для сцени та студії.', 'stock' => 6, 'image' => 'https://via.placeholder.com/520x320?text=Korg'],
            ['name' => 'Портативне піаніно Yamaha P-45', 'category' => 'Клавішні інструменти', 'price' => 16999.00, 'description' => 'Реалістичний звук та компактний дизайн для дому.', 'stock' => 5, 'image' => 'https://via.placeholder.com/520x320?text=Yamaha+P-45'],
            ['name' => 'MIDI-клавіатура Akai MPK Mini', 'category' => 'Клавішні інструменти', 'price' => 4999.00, 'description' => 'Універсальний MIDI контролер для створення музики.', 'stock' => 10, 'image' => 'https://via.placeholder.com/520x320?text=Akai+MPK+Mini'],
            ['name' => 'Цифрове піаніно Casio Privia', 'category' => 'Клавішні інструменти', 'price' => 21999.00, 'description' => 'Висока якість звучання та легка клавіатура.', 'stock' => 3, 'image' => 'https://via.placeholder.com/520x320?text=Casio+Privia'],
            ['name' => 'Клавішна станція Roland GO:PIANO', 'category' => 'Клавішні інструменти', 'price' => 8999.00, 'description' => 'Ідеальний інструмент для навчання та миттєвого створення музики.', 'stock' => 8, 'image' => 'https://via.placeholder.com/520x320?text=Roland+GO%3APIANO'],
            ['name' => 'Ударна установка Pearl Roadshow', 'category' => 'Ударні', 'price' => 25999.00, 'description' => 'Повний набір для сучасного барабанщика.', 'stock' => 2, 'image' => 'https://via.placeholder.com/520x320?text=Pearl+Roadshow'],
            ['name' => 'Електронна барабанна установка Alesis Nitro', 'category' => 'Ударні', 'price' => 13999.00, 'description' => 'Компактний електронний набір для тренувань і виступів.', 'stock' => 4, 'image' => 'https://via.placeholder.com/520x320?text=Alesis+Nitro'],
            ['name' => 'Барабанні палички Vic Firth', 'category' => 'Ударні', 'price' => 499.00, 'description' => 'Надійні палички для яскравого та точного звучання.', 'stock' => 30, 'image' => 'https://via.placeholder.com/520x320?text=Vic+Firth'],
            ['name' => 'Перкусійні бонго Meinl', 'category' => 'Ударні', 'price' => 7499.00, 'description' => 'Яскраві тони для живих виступів та студії.', 'stock' => 6, 'image' => 'https://via.placeholder.com/520x320?text=Meinl+Bongo'],
            ['name' => 'Тарілка Paiste PST5 16"', 'category' => 'Ударні', 'price' => 3499.00, 'description' => 'Стабільний яркий звук для рок та поп музики.', 'stock' => 9, 'image' => 'https://via.placeholder.com/520x320?text=Paiste+PST5+16'],
        ];

        foreach ($defaultProducts as $product) {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM products WHERE name = :name');
            $stmt->execute(['name' => $product['name']]);
            if (intval($stmt->fetchColumn()) === 0) {
                $insert = $this->pdo->prepare('INSERT INTO products (name, category, price, description, stock, image) VALUES (:name, :category, :price, :description, :stock, :image)');
                $insert->execute($product);
            }
        }

        if ($this->isEmpty('news')) {
            $this->pdo->exec(<<<SQL
INSERT INTO news (title, summary, content, date, image) VALUES
('Літній розпродаж музичних інструментів', 'Знижки до 25% на обрані гітари та клавішні.', 'Літній сезон починається з вигідних пропозицій — оберіть свій інструмент вже сьогодні.', '2026-06-01', 'https://via.placeholder.com/520x320?text=Sale'),
('Нові професійні барабани у каталозі', 'Модельні ряди для початківців та професіоналів.', 'Пропонуємо якісні ударні установки з доставкою по Україні.', '2026-05-25', 'https://via.placeholder.com/520x320?text=Drums');
SQL
            );
        }

        if ($this->isEmpty('users')) {
            $adminPassword = password_hash('music123', PASSWORD_DEFAULT);
            $customerPassword = password_hash('user123', PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare('INSERT INTO users (login, name, email, password, role, registered) VALUES (:login, :name, :email, :password, :role, :registered)');
            $stmt->execute([
                'login' => 'admin',
                'name' => 'Адміністратор',
                'email' => 'admin@example.com',
                'password' => $adminPassword,
                'role' => 'admin',
                'registered' => '2026-01-01'
            ]);
            $stmt->execute([
                'login' => 'user',
                'name' => 'Користувач',
                'email' => 'user@example.com',
                'password' => $customerPassword,
                'role' => 'user',
                'registered' => '2026-01-15'
            ]);
        }

        if ($this->isEmpty('gallery')) {
            $this->pdo->exec(<<<SQL
INSERT INTO gallery (title, image, caption) VALUES
('Вітрина гітар', 'https://via.placeholder.com/520x320?text=Guitar', 'Колекція акустичних та електричних гітар.'),
('Клавішні інструменти', 'https://via.placeholder.com/520x320?text=Keyboard', 'Розділ синтезаторів та піаніно.');
SQL
            );
        }

        if ($this->isEmpty('pages')) {
            $this->pdo->exec(<<<SQL
INSERT INTO pages (slug, title, content) VALUES
('about-us', 'Про нас', 'Ми пропонуємо музичні інструменти високої якості для музикантів усіх рівнів.'),
('contacts', 'Контакти', 'Звертайтеся до нас за телефоном або електронною поштою, і ми з радістю допоможемо.');
SQL
            );
        }
    }

    private function isEmpty(string $table): bool
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$table}");
        return intval($stmt->fetchColumn()) === 0;
    }
}
