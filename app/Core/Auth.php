<?php
require_once __DIR__ . '/../Models/UserModel.php';

class Auth
{
    private const SESSION_ROLE = 'user_role';
    private const SESSION_NAME = 'user_name';

    public static function check(): bool
    {
        return !empty($_SESSION[self::SESSION_ROLE]);
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION[self::SESSION_ROLE]) && $_SESSION[self::SESSION_ROLE] === 'admin';
    }

    public static function getUserName(): ?string
    {
        return $_SESSION[self::SESSION_NAME] ?? null;
    }

    public static function login(string $login, string $password): bool
    {
        $userModel = new UserModel();
        $user = $userModel->findByLogin($login);
        if ($user === null) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION[self::SESSION_ROLE] = $user['role'];
        $_SESSION[self::SESSION_NAME] = $user['login'];
        $_SESSION['user_id'] = $user['id'];

        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_ROLE], $_SESSION[self::SESSION_NAME], $_SESSION['user_id']);
    }
}
