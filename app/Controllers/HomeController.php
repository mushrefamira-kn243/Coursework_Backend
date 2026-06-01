<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/ProductModel.php';
require_once __DIR__ . '/../Models/NewsModel.php';
require_once __DIR__ . '/../Models/GalleryModel.php';
require_once __DIR__ . '/../Models/PageModel.php';

class HomeController extends Controller
{
    public function index()
    {
        $products = (new ProductModel())->getAll();
        $news = (new NewsModel())->getAll();
        $this->renderLayout('home', [
            'products' => array_slice($products, 0, 4),
            'news' => array_slice($news, 0, 3)
        ]);
    }

    public function catalog()
    {
        $items = (new ProductModel())->getAll();
        $this->renderLayout('catalog', ['items' => $items]);
    }

    public function news()
    {
        $items = (new NewsModel())->getAll();
        $this->renderLayout('news', ['items' => $items]);
    }

    public function gallery()
    {
        $items = (new GalleryModel())->getAll();
        $this->renderLayout('gallery', ['items' => $items]);
    }

    public function page()
    {
        $slug = trim($_GET['slug'] ?? '');
        $page = (new PageModel())->findBySlug($slug);
        if (!$page) {
            header('Location: index.php');
            exit;
        }
        $this->renderLayout('page', ['page' => $page]);
    }
}
