<section class="section-preview">
    <div class="section-header">
        <div>
            <h2>Фотогалерея</h2>
            <p class="note">Огляд музичних інструментів і музичної атмосфери в магазині.</p>
        </div>
        <a class="text-link" href="index.php">Повернутися на головну</a>
    </div>
    <div class="news-grid">
        <?php foreach ($items as $item): ?>
            <article class="news-card gallery-card" data-image="<?= htmlspecialchars($item['image']) ?>" data-title="<?= htmlspecialchars($item['title']) ?>" data-description="<?= htmlspecialchars($item['caption']) ?>">
                <div class="product-image" style="background-image: url('<?= htmlspecialchars($item['image']) ?>');"></div>
                <div class="product-body">
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars($item['caption']) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
