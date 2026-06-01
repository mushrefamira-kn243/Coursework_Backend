<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Core/Auth.php';
require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/NewsModel.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Models/GalleryModel.php';
require_once __DIR__ . '/../Models/PageModel.php';

class AdminController extends Controller
{
    private $modules = [
        'products' => 'Каталог продукції',
        'news' => 'Новини',
        'users' => 'Користувачі сайту',
        'gallery' => 'Фотогалерея',
        'pages' => 'Сторінки'
    ];

    public function index()
    {
        $this->requireLogin();
        // basic analytics
        $db = Database::getInstance()->getConnection();
        $stats = [];
        $stats['products'] = intval($db->query('SELECT COUNT(*) FROM products')->fetchColumn() ?? 0);
        $hasOrdersTable = intval($db->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='orders'")->fetchColumn() ?? 0);
        $stats['orders'] = $hasOrdersTable ? intval($db->query('SELECT COUNT(*) FROM orders')->fetchColumn() ?? 0) : 0;
        $stats['total_revenue'] = 0;
        try {
            $stats['total_revenue'] = floatval($db->query('SELECT IFNULL(SUM(total),0) FROM orders')->fetchColumn());
        } catch (Exception $e) {
            $stats['total_revenue'] = 0;
        }

        $stats['low_stock'] = [];
        try {
            $stmt = $db->query('SELECT id,name,stock FROM products WHERE stock<=3 ORDER BY stock ASC');
            $stats['low_stock'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $stats['low_stock'] = [];
        }

        $this->renderLayout('admin/dashboard', [
            'modules' => $this->modules,
            'stats' => $stats
        ]);
    }

    public function analytics()
    {
        $this->requireLogin();
        $db = Database::getInstance()->getConnection();
        $orders = [];
        $topProducts = [];
        $dailySales = [];

        try {
            $stmt = $db->query('SELECT o.id, o.user_id, o.total, o.created_at, u.name AS user_name FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC');
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $orders = [];
        }

        try {
            $stmt = $db->query('SELECT p.id, p.name, SUM(oi.qty) AS sold_qty, SUM(oi.qty * oi.price) AS revenue FROM order_items oi JOIN products p ON p.id = oi.product_id GROUP BY p.id ORDER BY sold_qty DESC LIMIT 10');
            $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $topProducts = [];
        }

        try {
            $stmt = $db->query("SELECT substr(created_at, 1, 10) AS day, COUNT(*) AS orders, SUM(total) AS revenue FROM orders GROUP BY day ORDER BY day DESC LIMIT 15");
            $dailySales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $dailySales = [];
        }

        $this->renderLayout('admin/analytics', [
            'orders' => $orders,
            'topProducts' => $topProducts,
            'dailySales' => $dailySales
        ]);
    }

    public function module()
    {
        $this->requireLogin();
        $module = $_GET['name'] ?? 'products';
        if (!isset($this->modules[$module])) {
            header('Location: index.php?route=admin');
            exit;
        }

        $data = $this->loadModuleData($module);
        $this->renderLayout('admin/' . $module, [
            'module' => $module,
            'title' => $this->modules[$module],
            'items' => $data
        ]);
    }

    private function loadModuleData(string $module): array
    {
        switch ($module) {
            case 'products':
                $model = new ProductModel();
                return $model->getAll();
            case 'news':
                return (new NewsModel())->getAll();
            case 'users':
                return (new UserModel())->getAll();
            case 'gallery':
                return (new GalleryModel())->getAll();
            case 'pages':
                return (new PageModel())->getAll();
            default:
                return [];
        }
    }

    private function requireLogin()
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            header('Location: index.php?route=login');
            exit;
        }
    }
}
