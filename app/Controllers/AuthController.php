<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Core/Auth.php';
require_once __DIR__ . '/../Models/UserModel.php';

class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (Auth::login($login, $password)) {
                if (Auth::isAdmin()) {
                    header('Location: index.php?route=admin');
                } else {
                    header('Location: index.php');
                }
                exit;
            }

            $this->renderLayout('login', ['error' => 'Невірний логін або пароль']);
            return;
        }

        $this->renderLayout('login');
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm = trim($_POST['confirm_password'] ?? '');

            if ($password !== $confirm) {
                $this->renderLayout('register', [
                    'error' => 'Паролі не співпадають',
                    'data' => $_POST
                ]);
                return;
            }

            $userModel = new UserModel();
            $result = $userModel->save([
                'login' => $login,
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'user',
                'registered' => date('Y-m-d')
            ]);

            if ($result['success']) {
                Auth::login($login, $password);
                header('Location: index.php');
                exit;
            }

            $this->renderLayout('register', ['error' => $result['message'], 'data' => $_POST]);
            return;
        }

        $this->renderLayout('register');
    }

    public function logout()
    {
        Auth::logout();
        header('Location: index.php');
        exit;
    }
}
