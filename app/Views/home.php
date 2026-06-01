<section class="hero">
    <div class="hero-panel">
        <span class="eyebrow">Інтернет-магазин музичних інструментів</span>
        <h1>Сучасний магазин для музикантів і прихильників музики</h1>
        <p>Цей магазин демонструє роботу MVC-системи з адміністративною панеллю. Тут ви можете переглядати каталог, читати новини, дивитися галерею та заходити як користувач або адміністратор.</p>
        <div class="hero-cta">
            <a class="button" href="index.php?route=login">Увійти</a>
            <a class="button button-secondary" href="index.php?route=catalog">Перейти в каталог</a>
        </div>
    </div>
    <div class="hero-grid">
        <div class="hero-card">
            <h2>Головний каталог</h2>
            <p>Оберіть інструмент та дізнайтеся більше про характеристики і наявність.</p>
            <a href="index.php?route=catalog" class="button button-secondary">Переглянути</a>
        </div>
        <div class="hero-card">
            <h2>Останні новини</h2>
            <p>Читайте свіжі анонси про акції, новинки та події у світі музики.</p>
            <a href="index.php?route=news" class="button button-secondary">Читати</a>
        </div>
        <div class="hero-card">
            <h2>Фотогалерея</h2>
            <p>Дивіться красиві знімки інструментів та музичних сцен.</p>
            <a href="index.php?route=gallery" class="button button-secondary">Дивитися</a>
        </div>
        <div class="hero-card">
            <h2>Сторінки</h2>
            <p>Дізнайтеся більше про нас, умови та порівняння.</p>
            <a href="index.php?route=page&slug=about-us" class="button button-secondary">Детальніше</a>
        </div>
    </div>
</section>

<section class="section-preview">
    <div class="section-header">
        <h2>Нові надходження</h2>
        <a class="text-link" href="index.php?route=catalog">Всі товари</a>
    </div>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card" data-item='<?= htmlspecialchars(json_encode($product, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                <button class="quick-add" data-id="<?= intval($product['id']) ?>">+</button>
                <div class="product-image" style="background-image: url('<?= htmlspecialchars($product['image'] ?? 'https://via.placeholder.com/420x260?text=' . urlencode($product['name'])) ?>');"></div>
                <div class="product-body">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= htmlspecialchars($product['category']) ?></p>
                    <strong><?= number_format(floatval($product['price']), 2, '.', ' ') ?> ₴</strong>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section-preview">
    <div class="section-header">
        <h2>Останні новини</h2>
        <a class="text-link" href="index.php?route=news">Всі новини</a>
    </div>
    <div class="news-grid">
        <?php foreach ($news as $item): ?>
            <article class="news-card">
                <?php if (!empty($item['image'])): ?>
                    <div class="product-image" style="background-image: url('<?= htmlspecialchars($item['image']) ?>');"></div>
                <?php endif; ?>
                <div class="product-body">
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars($item['summary']) ?></p>
                    <small><?= htmlspecialchars($item['date']) ?></small>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>