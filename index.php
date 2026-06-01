<?php
session_start();

require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Model.php';
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Core/Auth.php';
require_once __DIR__ . '/app/Controllers/HomeController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/AdminController.php';
require_once __DIR__ . '/app/Controllers/AjaxController.php';

$path = isset($_GET['route']) ? $_GET['route'] : 'home';

switch ($path) {
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;
    case 'register':
        $controller = new AuthController();
        $controller->register();
        break;
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    case 'admin':
        $controller = new AdminController();
        $controller->index();
        break;
    case 'admin_analytics':
        $controller = new AdminController();
        $controller->analytics();
        break;
    case 'module':
        $controller = new AdminController();
        $controller->module();
        break;
    case 'catalog':
        $controller = new HomeController();
        $controller->catalog();
        break;
    case 'news':
        $controller = new HomeController();
        $controller->news();
        break;
    case 'gallery':
        $controller = new HomeController();
        $controller->gallery();
        break;
    case 'page':
        $controller = new HomeController();
        $controller->page();
        break;
    case 'ajax':
        $controller = new AjaxController();
        $controller->process();
        break;
    default:
        $controller = new HomeController();
        $controller->index();
        break;
}
