<section class="section-preview">
    <div class="section-header">
        <div>
            <h2><?= htmlspecialchars($page['title']) ?></h2>
            <p class="note"><?= htmlspecialchars($page['slug']) ?></p>
        </div>
        <a class="text-link" href="index.php">Повернутися на головну</a>
    </div>
    <div class="product-card" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);">
        <div class="product-body">
            <p><?= nl2br(htmlspecialchars($page['content'])) ?></p>
        </div>
    </div>
</section>
