<section class="admin-module">
    <div class="module-header">
        <div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p>Оновлюйте фотогалерею магазину з описами та зображеннями.</p>
        </div>
        <div class="admin-actions">
            <a class="button" href="index.php?route=admin">Назад до панелі</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" data-module="gallery">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Фото</th>
                    <th>Підпис</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr data-id="<?= intval($item['id']) ?>" data-item='<?= htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                        <td><?= intval($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><img class="table-thumb" src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>"></td>
                        <td><?= htmlspecialchars($item['caption']) ?></td>
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
        <h2>Додати / редагувати фото</h2>
        <form id="admin-form" data-module="gallery">
            <input type="hidden" name="id" value="0">
            <label>Назва<input type="text" name="title" required></label>
            <label>URL зображення<input type="url" name="image" required></label>
            <label>Підпис<textarea name="caption"></textarea></label>
            <button type="submit" class="button">Зберегти</button>
        </form>
    </div>
</section>
