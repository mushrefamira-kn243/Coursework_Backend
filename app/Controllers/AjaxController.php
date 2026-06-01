<?php
require_once __DIR__ . '/../Core/Auth.php';
require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/NewsModel.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Models/GalleryModel.php';
require_once __DIR__ . '/../Models/PageModel.php';

class AjaxController
{
    public function process()
    {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Недійсний метод']);
            return;
        }

        $action = $_POST['action'] ?? '';
        $module = $_POST['module'] ?? '';
        $adminActions = ['load', 'save', 'delete'];

        if (in_array($action, $adminActions, true)) {
            if (!Auth::check() || !Auth::isAdmin()) {
                echo json_encode(['success' => false, 'message' => 'Недостатньо прав']);
                return;
            }
        }

        $result = ['success' => false, 'message' => 'Невідома дія'];

        switch ($action) {
            case 'load':
                $result = ['success' => true, 'data' => $this->loadModuleData($module)];
                break;
            case 'save':
                $result = $this->saveItem($module, $_POST);
                break;
            case 'cart_add':
                $result = $this->addToCart($_POST);
                break;
            case 'cart_get':
                $result = $this->getCart();
                break;
            case 'cart_remove':
                $result = $this->removeFromCart($_POST);
                break;
            case 'checkout':
                $result = $this->checkout($_POST);
                break;
            case 'comments_add':
                $result = $this->addComment($_POST);
                break;
            case 'comments_get':
                $result = $this->getComments($_POST);
                break;
            case 'delete':
                $result = $this->deleteItem($module, intval($_POST['id'] ?? 0));
                break;
        }

        echo json_encode($result);
    }

    private function loadModuleData(string $module): array
    {
        switch ($module) {
            case 'products':
                return (new ProductModel())->getAll();
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

    private function saveItem(string $module, array $data): array
    {
        switch ($module) {
            case 'products':
                return (new ProductModel())->save($data);
            case 'news':
                return (new NewsModel())->save($data);
            case 'users':
                return (new UserModel())->save($data);
            case 'gallery':
                return (new GalleryModel())->save($data);
            case 'pages':
                return (new PageModel())->save($data);
            default:
                return ['success' => false, 'message' => 'Невідомий модуль'];
        }
    }

    private function deleteItem(string $module, int $id): array
    {
        switch ($module) {
            case 'products':
                return (new ProductModel())->delete($id);
            case 'news':
                return (new NewsModel())->delete($id);
            case 'users':
                return (new UserModel())->delete($id);
            case 'gallery':
                return (new GalleryModel())->delete($id);
            case 'pages':
                return (new PageModel())->delete($id);
            default:
                return ['success' => false, 'message' => 'Невідомий модуль'];
        }
    }

    private function addToCart(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        $qty = intval($data['qty'] ?? 1);
        if ($id <= 0 || $qty <= 0) {
            return ['success' => false, 'message' => 'Невірні дані'];
        }
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (!isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = 0;
        $_SESSION['cart'][$id] += $qty;
        return ['success' => true, 'message' => 'Додано до кошика'];
    }

    private function getCart(): array
    {
        $cart = $_SESSION['cart'] ?? [];
        $items = [];
        foreach ($cart as $productId => $qty) {
            $model = new ProductModel();
            $p = $model->findById(intval($productId));
            if ($p) {
                $p['qty'] = $qty;
                $items[] = $p;
            }
        }
        return ['success' => true, 'cart' => $items];
    }

    private function removeFromCart(array $data): array
    {
        $id = intval($data['id'] ?? 0);
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        return ['success' => true];
    }

    private function checkout(array $data): array
    {
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) return ['success' => false, 'message' => 'Кошик порожній'];
        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();
            $total = 0;
            foreach ($cart as $pid => $qty) {
                $p = (new ProductModel())->findById($pid);
                if (!$p) continue;
                $total += $p['price'] * $qty;
            }
            $stmt = $db->prepare('INSERT INTO orders (user_id, total, created_at) VALUES (:user_id, :total, :created_at)');
            $stmt->execute(['user_id' => $_SESSION['user_id'] ?? null, 'total' => $total, 'created_at' => date('Y-m-d H:i:s')]);
            $orderId = intval($db->lastInsertId());
            foreach ($cart as $pid => $qty) {
                $p = (new ProductModel())->findById($pid);
                if (!$p) continue;
                $stmt = $db->prepare('INSERT INTO order_items (order_id, product_id, qty, price) VALUES (:order_id, :product_id, :qty, :price)');
                $stmt->execute(['order_id'=>$orderId,'product_id'=>$pid,'qty'=>$qty,'price'=>$p['price']]);
               
                $db->prepare('UPDATE products SET stock = stock - :qty WHERE id = :id')->execute(['qty'=>$qty,'id'=>$pid]);
            }
            $db->commit();
            (new ProductModel())->exportToJson();
            unset($_SESSION['cart']);
            return ['success' => true, 'message' => 'Замовлення оформлено'];
        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => 'Помилка оформлення'];
        }
    }

    private function addComment(array $data): array
    {
        $product_id = intval($data['product_id'] ?? 0);
        $author = trim($data['author'] ?? '');
        $rating = intval($data['rating'] ?? 5);
        $comment = trim($data['comment'] ?? '');
        if ($product_id <= 0 || $author === '') return ['success'=>false,'message'=>'Невірні дані'];
        $stmt = Database::getInstance()->getConnection()->prepare('INSERT INTO comments (product_id, author, rating, comment, created_at) VALUES (:product_id,:author,:rating,:comment,:created_at)');
        $stmt->execute(['product_id'=>$product_id,'author'=>$author,'rating'=>$rating,'comment'=>$comment,'created_at'=>date('Y-m-d H:i:s')]);
        return ['success'=>true,'message'=>'Коментар додано'];
    }

    private function getComments(array $data): array
    {
        $product_id = intval($data['product_id'] ?? 0);
        $stmt = Database::getInstance()->getConnection()->prepare('SELECT * FROM comments WHERE product_id = :product_id ORDER BY id DESC');
        $stmt->execute(['product_id'=>$product_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['success'=>true,'comments'=>$rows];
    }
}
