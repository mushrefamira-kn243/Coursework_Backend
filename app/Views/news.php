<section class="section-preview">
    <div class="section-header">
        <div>
            <h2>Новини магазину</h2>
            <p class="note">Стежте за останніми акціями, оновленнями і подіями.</p>
        </div>
        <a class="text-link" href="index.php">Повернутися на головну</a>
    </div>
    <div class="news-grid">
        <?php foreach ($items as $item): ?>
            <article class="news-card news-article" data-image="<?= htmlspecialchars($item['image'] ?? '') ?>" data-title="<?= htmlspecialchars($item['title']) ?>" data-description="<?= htmlspecialchars($item['content'] ?: $item['summary']) ?>" data-date="<?= htmlspecialchars($item['date']) ?>">
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
