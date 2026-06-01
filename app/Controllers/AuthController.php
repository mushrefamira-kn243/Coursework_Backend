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

    public function profile()
    {
        if (!Auth::check()) {
            header('Location: index.php?route=login');
            exit;
        }

        $userModel = new UserModel();
        $user = $userModel->findById(intval($_SESSION['user_id'] ?? 0));
        if (!$user) {
            header('Location: index.php');
            exit;
        }

        $message = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $avatarPath = $user['avatar'] ?? '';

            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['avatar']['tmp_name'];
                $filename = basename($_FILES['avatar']['name']);
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed = ['png', 'jpg', 'jpeg', 'gif'];
                if (in_array($ext, $allowed, true)) {
                    $uploadDir = __DIR__ . '/../../uploads/avatars';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $newName = 'avatar_' . intval($user['id']) . '_' . time() . '.' . $ext;
                    $destination = $uploadDir . '/' . $newName;
                    if (move_uploaded_file($tmp, $destination)) {
                        $avatarPath = 'uploads/avatars/' . $newName;
                    }
                }
            }

            $profileData = [
                'id' => intval($user['id']),
                'login' => $user['login'] ?? '',
                'name' => $name,
                'email' => $email,
                'role' => $user['role'] ?? 'user',
                'registered' => $user['registered'] ?? date('Y-m-d'),
                'avatar' => $avatarPath
            ];

            if (trim($_POST['password'] ?? '') !== '') {
                $profileData['password'] = trim($_POST['password']);
            }

            $result = $userModel->save($profileData);
            $message = $result['message'] ?? null;
            if ($result['success']) {
                $user = $userModel->findById(intval($user['id']));
            }
        }

        $this->renderLayout('profile', [
            'user' => $user,
            'message' => $message
        ]);
    }
}
