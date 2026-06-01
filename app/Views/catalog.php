<?php
$categories = array_unique(array_map(function($item) { return $item['category'] ?? ''; }, $items));
sort($categories);
?>
<section class="section-preview">
    <div class="section-header">
        <div>
            <h2>Каталог продукції</h2>
            <p class="note">Перелік інструментів та аксесуарів для різного рівня музикантів.</p>
        </div>
        <a class="text-link" href="index.php">Повернутися на головну</a>
    </div>
    <div class="catalog-search-panel">
        <form id="catalog-search-form" class="catalog-search-form" action="javascript:void(0)">
            <input id="catalog-search-name" type="search" placeholder="Шукати за назвою..." autocomplete="off">
            <select id="catalog-search-category" style="background-color: #1a1f2c; color: #ffffff; border: 1px solid #3f4756;">
                <option value="" style="background-color: #1a1f2c; color: #ffffff;">Усі категорії</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category) ?>" style="background-color: #1a1f2c; color: #ffffff;"><?= htmlspecialchars($category) ?></option>
                <?php endforeach; ?>
            </select>
            <input id="catalog-search-min-price" type="number" placeholder="Мін. ціна" min="0" step="100">
            <input id="catalog-search-max-price" type="number" placeholder="Макс. ціна" min="0" step="100">
        </form>
    </div>
    <div class="product-grid">
        <?php foreach ($items as $item): ?>
            <article class="product-card" data-item='<?= htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                <button class="quick-add" data-id="<?= intval($item['id']) ?>">+</button>
                <div class="product-image" style="background-image: url('<?= htmlspecialchars($item['image'] ?? 'https://via.placeholder.com/520x320?text=' . urlencode($item['name'])) ?>');"></div>
                <div class="product-body">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p><?= htmlspecialchars($item['category']) ?></p>
                    <strong><?= number_format(floatval($item['price']), 2, '.', ' ') ?> ₴</strong>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <div id="catalog-search-message" class="catalog-search-message" style="display:none;">Інструменти не знайдено. Спробуйте інші параметри пошуку.</div>
</section>