<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musixx</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-user-role="<?= Auth::isAdmin() ? 'admin' : (Auth::check() ? 'user' : 'guest') ?>">
<header class="site-header">
    <div class="container header-inner">
        <div>
            <a href="index.php" class="brand">Musixx</a>
            <p class="subtitle">Система інтернет-магазину музичних інструментів</p>
        </div>
        <nav>
            <a href="index.php">Головна</a>
            <a href="index.php?route=catalog">Каталог</a>
            <a href="index.php?route=news">Новини</a>
            <a href="index.php?route=gallery">Галерея</a>
            <a href="index.php?route=page&slug=about-us">Про нас</a>
            <a href="javascript:void(0)" id="theme-toggle" title="Перемкнути тему">🌗</a>
            <a href="javascript:void(0)" id="cart-link">Кошик (<span id="cart-count">0</span>)</a>
            <?php if (Auth::check()): ?>
                <a href="index.php?route=profile">Профіль</a>
                <?php if (Auth::isAdmin()): ?>
                    <a href="index.php?route=admin">Панель</a>
                <?php endif; ?>
                <a href="index.php?route=logout">Вийти</a>
            <?php else: ?>
                <a href="index.php?route=login">Увійти</a>
                <a href="index.php?route=register">Реєстрація</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
    <?php if (isset($error) && $error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php $content(); ?>
</main>
<footer class="footer">
    <div class="container">© 2026 Інтернет-магазин «Musixx»</div>
</footer>
<script src="assets/js/app.js"></script>
</body>
</html>