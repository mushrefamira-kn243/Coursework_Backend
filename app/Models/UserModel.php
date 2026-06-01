<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    protected $table = 'users';

    public function findByLogin(string $login): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE login = :login LIMIT 1');
        $stmt->execute(['login' => $login]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        return $item ?: null;
    }

    public function save(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        $login = trim($data['login'] ?? '');
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $role = trim($data['role'] ?? 'user');
        if (!in_array($role, ['admin', 'user'], true)) {
            $role = 'user';
        }
        $registered = trim($data['registered'] ?? date('Y-m-d'));
        $password = trim($data['password'] ?? '');
        $avatar = trim($data['avatar'] ?? '');

        if ($login === '') {
            return ['success' => false, 'message' => 'Логін обов’язковий'];
        }

        if ($id === 0 && $password === '') {
            return ['success' => false, 'message' => 'Пароль обов’язковий'];
        }

        $existingUser = $this->findByLogin($login);
        if ($existingUser && $existingUser['id'] != $id) {
            return ['success' => false, 'message' => 'Такий логін вже існує'];
        }

        if ($id > 0 && $avatar === '') {
            $current = $this->findById($id);
            $avatar = $current['avatar'] ?? '';
        }

        if ($id === 0) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare('INSERT INTO users (login, name, email, password, role, registered, avatar) VALUES (:login, :name, :email, :password, :role, :registered, :avatar)');
            $stmt->execute([
                'login' => $login,
                'name' => $name,
                'email' => $email,
                'password' => $passwordHash,
                'role' => $role,
                'registered' => $registered,
                'avatar' => $avatar
            ]);
            $id = intval($this->db->lastInsertId());
        } else {
            $passwordHash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;
            if ($passwordHash !== null) {
                $stmt = $this->db->prepare('UPDATE users SET login = :login, name = :name, email = :email, password = :password, role = :role, registered = :registered, avatar = :avatar WHERE id = :id');
                $stmt->execute([
                    'login' => $login,
                    'name' => $name,
                    'email' => $email,
                    'password' => $passwordHash,
                    'role' => $role,
                    'registered' => $registered,
                    'avatar' => $avatar,
                    'id' => $id
                ]);
            } else {
                $stmt = $this->db->prepare('UPDATE users SET login = :login, name = :name, email = :email, role = :role, registered = :registered, avatar = :avatar WHERE id = :id');
                $stmt->execute([
                    'login' => $login,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'registered' => $registered,
                    'avatar' => $avatar,
                    'id' => $id
                ]);
            }
        }

        $this->exportToJson();

        return ['success' => true, 'message' => 'Користувача збережено', 'item' => [
            'id' => $id,
            'login' => $login,
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'registered' => $registered,
            'avatar' => $avatar
        ]];
    }

    public function delete(int $id): array
    {
        $user = $this->findById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'Користувача не знайдено'];
        }
        if (($user['role'] ?? '') === 'admin') {
            return ['success' => false, 'message' => 'Адміністратора не можна видалити'];
        }
        return parent::delete($id);
    }
}
