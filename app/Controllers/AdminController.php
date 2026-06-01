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
        'orders' => 'Замовлення',
        'news' => 'Новини',
        'users' => 'Користувачі сайту',
        'gallery' => 'Фотогалерея'
    ];

    public function index()
    {
        $this->requireLogin();
         
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

        $period = $_GET['period'] ?? '30';
        $category = trim($_GET['category'] ?? '');
        $minPrice = floatval($_GET['min_price'] ?? 0);
        $maxPrice = floatval($_GET['max_price'] ?? 0);
        $startDate = '';
        $endDate = '';

        $now = new DateTime();
        switch ($period) {
            case '3':
                $startDate = (new DateTime('-2 days'))->format('Y-m-d 00:00:00');
                $endDate = $now->format('Y-m-d 23:59:59');
                break;
            case '7':
                $startDate = (new DateTime('-6 days'))->format('Y-m-d 00:00:00');
                $endDate = $now->format('Y-m-d 23:59:59');
                break;
            case '30':
                $startDate = (new DateTime('-29 days'))->format('Y-m-d 00:00:00');
                $endDate = $now->format('Y-m-d 23:59:59');
                break;
            case 'month':
                $startDate = (new DateTime('first day of this month'))->format('Y-m-d 00:00:00');
                $endDate = $now->format('Y-m-d 23:59:59');
                break;
            default:
                $startDate = '';
                $endDate = '';
                break;
        }

        $params = [];
        $where = 'o.status = "completed"';
        if ($startDate !== '') {
            $where .= ' AND o.created_at >= :start_date';
            $params['start_date'] = $startDate;
        }
        if ($endDate !== '') {
            $where .= ' AND o.created_at <= :end_date';
            $params['end_date'] = $endDate;
        }
        if ($category !== '') {
            $where .= ' AND p.category = :category';
            $params['category'] = $category;
        }
        if ($minPrice > 0) {
            $where .= ' AND oi.price >= :min_price';
            $params['min_price'] = $minPrice;
        }
        if ($maxPrice > 0) {
            $where .= ' AND oi.price <= :max_price';
            $params['max_price'] = $maxPrice;
        }

        $orders = [];
        $topProducts = [];
        $dailySales = [];
        $topCategories = [];

        try {
            $stmt = $db->prepare(
                'SELECT o.id, u.name AS user_name, SUM(oi.qty * oi.price) AS total, o.status, o.created_at
                 FROM orders o
                 JOIN order_items oi ON oi.order_id = o.id
                 JOIN products p ON p.id = oi.product_id
                 LEFT JOIN users u ON u.id = o.user_id
                 WHERE ' . $where . '
                 GROUP BY o.id
                 HAVING SUM(oi.qty * oi.price) > 0
                 ORDER BY o.created_at DESC'
            );
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $orders = [];
        }

        try {
            $stmt = $db->prepare(
                'SELECT p.id, p.name, SUM(oi.qty) AS sold_qty, SUM(oi.qty * oi.price) AS revenue
                 FROM order_items oi
                 JOIN orders o ON oi.order_id = o.id
                 JOIN products p ON p.id = oi.product_id
                 WHERE ' . $where . '
                 GROUP BY p.id
                 ORDER BY sold_qty DESC
                 LIMIT 10'
            );
            $stmt->execute($params);
            $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $topProducts = [];
        }

        try {
            $stmt = $db->prepare(
                'SELECT substr(o.created_at, 1, 10) AS day, COUNT(DISTINCT o.id) AS orders, SUM(oi.qty * oi.price) AS revenue
                 FROM order_items oi
                 JOIN orders o ON oi.order_id = o.id
                 JOIN products p ON p.id = oi.product_id
                 WHERE ' . $where . '
                 GROUP BY day
                 ORDER BY day DESC
                 LIMIT 15'
            );
            $stmt->execute($params);
            $dailySales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $dailySales = [];
        }

        try {
            $stmt = $db->prepare(
                'SELECT p.category, SUM(oi.qty) AS units, SUM(oi.qty * oi.price) AS revenue
                 FROM order_items oi
                 JOIN orders o ON oi.order_id = o.id
                 JOIN products p ON p.id = oi.product_id
                 WHERE ' . $where . '
                 GROUP BY p.category
                 ORDER BY revenue DESC
                 LIMIT 8'
            );
            $stmt->execute($params);
            $topCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $topCategories = [];
        }

        try {
            $stmt = $db->query('SELECT DISTINCT category FROM products ORDER BY category ASC');
            $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            $categories = [];
        }

        $this->renderLayout('admin/analytics', [
            'orders' => $orders,
            'topProducts' => $topProducts,
            'dailySales' => $dailySales,
            'topCategories' => $topCategories,
            'categories' => $categories,
            'filters' => [
                'period' => $period,
                'category' => $category,
                'min_price' => $minPrice,
                'max_price' => $maxPrice
            ]
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
                return (new ProductModel())->getAll();
            case 'orders':
                $db = Database::getInstance()->getConnection();
                $stmt = $db->query('SELECT o.id, o.user_id, o.total, o.status, o.created_at, u.name AS user_name FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC');
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'news':
                return (new NewsModel())->getAll();
            case 'users':
                return (new UserModel())->getAll();
            case 'gallery':
                return (new GalleryModel())->getAll();
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
