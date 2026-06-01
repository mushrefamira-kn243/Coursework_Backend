<section class="admin-module">
    <div class="module-header">
        <div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p>Керуйте каталогом інструментів — додавайте, редагуйте та видаляйте записи.</p>
        </div>
        <div class="admin-actions">
            <a class="button" href="index.php?route=admin">Назад до панелі</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" data-module="products">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Категорія</th>
                    <th>Ціна</th>
                    <th>Фото</th>
                    <th>Наявність</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr data-id="<?= intval($item['id']) ?>" data-item='<?= htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                        <td><?= intval($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td><?= number_format(floatval($item['price']), 2, '.', ' ') ?> ₴</td>
                        <td><img class="table-thumb" src="<?= htmlspecialchars($item['image'] ?? 'https://via.placeholder.com/120x80?text=Фото') ?>" alt="<?= htmlspecialchars($item['name']) ?>"></td>
                        <td><?= intval($item['stock']) ?></td>
                        <td>
                            <button class="button button-small" data-action="edit">Редагувати</button>
                            <button class="button button-small button-secondary" data-action="delete">Видалити</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-card">
        <h2>Додати / редагувати товар</h2>
        <form id="admin-form" data-module="products">
            <input type="hidden" name="id" value="0">
            <label>Назва інструменту<input type="text" name="name" required></label>
            <label>Категорія<input type="text" name="category" required></label>
            <label>Ціна<input type="number" step="0.01" name="price" required></label>
            <label>Фото (URL)<input type="url" name="image" placeholder="https://..."></label>
            <label>Наявність<input type="number" name="stock" required></label>
            <label>Опис<textarea name="description"></textarea></label>
            <button type="submit" class="button">Зберегти</button>
        </form>
    </div>
</section>
