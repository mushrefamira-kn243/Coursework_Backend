<section class="admin-page">
    <aside class="admin-sidebar">
        <div class="admin-user">
            <span>Увійшов як</span>
            <strong><?= htmlspecialchars(Auth::getUserName() ?? 'Гість') ?></strong>
        </div>
        <nav class="admin-nav">
            <a class="admin-nav-link active" href="index.php?route=admin">Головна</a>
            <a class="admin-nav-link" href="index.php?route=admin_analytics">Аналітика продажів</a>
            <a class="admin-nav-link" href="index.php?route=module&name=products">Товари</a>
            <a class="admin-nav-link" href="index.php?route=module&name=orders">Замовлення</a>
            <a class="admin-nav-link" href="index.php?route=module&name=news">Новини</a>
            <a class="admin-nav-link" href="index.php?route=module&name=gallery">Галерея</a>
            <a class="admin-nav-link" href="index.php?route=module&name=pages">Сторінки</a>
            <a class="admin-nav-link" href="index.php?route=module&name=users">Користувачі</a>
        </nav>
    </aside>
    <div class="admin-main">
        <section class="admin-panel">
            <h1>Панель адміністратора</h1>
            <p>Оберіть модуль для керування контентом.</p>
            <div class="admin-grid">
                <?php foreach ($modules as $key => $label): ?>
                    <a class="module-card" href="index.php?route=module&name=<?= htmlspecialchars($key) ?>">
                        <h3><?= htmlspecialchars($label) ?></h3>
                        <p>Управління <?= htmlspecialchars(mb_strtolower($label, 'UTF-8')) ?>.</p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</section>