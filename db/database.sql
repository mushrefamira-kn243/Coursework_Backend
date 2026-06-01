-- SQLite SQL script for initializing the instrument shop database
-- Run this script in SQLite or any compatible SQL environment.

PRAGMA foreign_keys = OFF;
BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category TEXT NOT NULL,
    price REAL NOT NULL,
    description TEXT,
    stock INTEGER NOT NULL,
    image TEXT DEFAULT ''
);

CREATE TABLE IF NOT EXISTS news (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    summary TEXT NOT NULL,
    content TEXT,
    date TEXT NOT NULL,
    image TEXT
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    role TEXT NOT NULL,
    registered TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS gallery (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    image TEXT NOT NULL,
    caption TEXT
);

CREATE TABLE IF NOT EXISTS pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug TEXT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    content TEXT
);

-- Orders and order items
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    total REAL NOT NULL,
    created_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    qty INTEGER NOT NULL,
    price REAL NOT NULL
);

-- Comments for products
CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    author TEXT NOT NULL,
    rating INTEGER NOT NULL DEFAULT 5,
    comment TEXT,
    created_at TEXT NOT NULL
);

INSERT INTO products (name, category, price, description, stock, image) VALUES
('Акустична гітара Yamaha F310', 'Гітари', 4999.00, 'Універсальна акустична гітара для початківців.', 8, 'https://via.placeholder.com/520x320?text=Yamaha+F310'),
('Електрогітара Fender Stratocaster', 'Гітари', 18999.00, 'Класичний звук та зручний гриф.', 4, 'https://via.placeholder.com/520x320?text=Fender+Stratocaster'),
('Бас-гітара Ibanez GSR200', 'Гітари', 14999.00, 'Легка та збалансована бас-гітара для ритму та соло.', 5, 'https://via.placeholder.com/520x320?text=Ibanez+GSR200'),
('Класична гітара Cordoba C3M', 'Гітари', 7999.00, 'Комфортний гриф та теплий звук для класичної музики.', 7, 'https://via.placeholder.com/520x320?text=Cordoba+C3M'),
('Укулеле Kala KA-15S', 'Гітари', 2599.00, 'Компактний інструмент для подорожей та навчання.', 12, 'https://via.placeholder.com/520x320?text=Kala+KA-15S'),
('Клавішний синтезатор Korg', 'Клавішні інструменти', 12999.00, 'Портативний синтезатор для сцени та студії.', 6, 'https://via.placeholder.com/520x320?text=Korg'),
('Портативне піаніно Yamaha P-45', 'Клавішні інструменти', 16999.00, 'Реалістичний звук та компактний дизайн для дому.', 5, 'https://via.placeholder.com/520x320?text=Yamaha+P-45'),
('MIDI-клавіатура Akai MPK Mini', 'Клавішні інструменти', 4999.00, 'Універсальний MIDI контролер для створення музики.', 10, 'https://via.placeholder.com/520x320?text=Akai+MPK+Mini'),
('Цифрове піаніно Casio Privia', 'Клавішні інструменти', 21999.00, 'Висока якість звучання та легка клавіатура.', 3, 'https://via.placeholder.com/520x320?text=Casio+Privia'),
('Клавішна станція Roland GO:PIANO', 'Клавішні інструменти', 8999.00, 'Ідеальний інструмент для навчання та миттєвого створення музики.', 8, 'https://via.placeholder.com/520x320?text=Roland+GO%3APIANO'),
('Ударна установка Pearl Roadshow', 'Ударні', 25999.00, 'Повний набір для сучасного барабанщика.', 2, 'https://via.placeholder.com/520x320?text=Pearl+Roadshow'),
('Електронна барабанна установка Alesis Nitro', 'Ударні', 13999.00, 'Компактний електронний набір для тренувань і виступів.', 4, 'https://via.placeholder.com/520x320?text=Alesis+Nitro'),
('Барабанні палички Vic Firth', 'Ударні', 499.00, 'Надійні палички для яскравого та точного звучання.', 30, 'https://via.placeholder.com/520x320?text=Vic+Firth'),
('Перкусійні бонго Meinl', 'Ударні', 7499.00, 'Яскраві тони для живих виступів та студії.', 6, 'https://via.placeholder.com/520x320?text=Meinl+Bongo'),
('Тарілка Paiste PST5 16"', 'Ударні', 3499.00, 'Стабільний яркий звук для рок та поп музики.', 9, 'https://via.placeholder.com/520x320?text=Paiste+PST5+16')
;

INSERT INTO news (title, summary, content, date, image) VALUES
('Літній розпродаж музичних інструментів', 'Знижки до 25% на обрані гітари та клавішні.', 'Літній сезон починається з вигідних пропозицій — оберіть свій інструмент вже сьогодні.', '2026-06-01', 'https://via.placeholder.com/520x320?text=Sale'),
('Нові професійні барабани у каталозі', 'Модельні ряди для початківців та професіоналів.', 'Пропонуємо якісні ударні установки з доставкою по Україні.', '2026-05-25', 'https://via.placeholder.com/520x320?text=Drums');

INSERT INTO users (name, email, role, registered) VALUES
('Олена Петренко', 'olena@example.com', 'user', '2026-01-15'),
('Іван Коваль', 'ivan@example.com', 'user', '2025-12-03');

INSERT INTO gallery (title, image, caption) VALUES
('Вітрина гітар', 'https://via.placeholder.com/520x320?text=Guitar', 'Колекція акустичних та електричних гітар.'),
('Клавішні інструменти', 'https://via.placeholder.com/520x320?text=Keyboard', 'Розділ синтезаторів та піаніно.');

INSERT INTO pages (slug, title, content) VALUES
('about-us', 'Про нас', 'Ми пропонуємо музичні інструменти високої якості для музикантів усіх рівнів.'),
('contacts', 'Контакти', 'Звертайтеся до нас за телефоном або електронною поштою, і ми з радістю допоможемо.');

COMMIT;
PRAGMA foreign_keys = ON;
