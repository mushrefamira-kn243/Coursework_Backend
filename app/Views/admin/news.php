<section class="admin-module">
    <div class="module-header">
        <div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p>Публікуйте новини магазину та оголошення для клієнтів.</p>
        </div>
        <div class="admin-actions">
            <a class="button" href="index.php?route=admin">Назад до панелі</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" data-module="news">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Заголовок</th>
                    <th>Короткий опис</th>
                    <th>Фото</th>
                    <th>Дата</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr data-id="<?= intval($item['id']) ?>" data-item='<?= htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                        <td><?= intval($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><?= htmlspecialchars($item['summary']) ?></td>
                        <td><img class="table-thumb" src="<?= htmlspecialchars($item['image'] ?? 'https://via.placeholder.com/120x80?text=Новина') ?>" alt="<?= htmlspecialchars($item['title']) ?>"></td>
                        <td><?= htmlspecialchars($item['date']) ?></td>
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
        <h2>Додати / редагувати новину</h2>
        <form id="admin-form" data-module="news">
            <input type="hidden" name="id" value="0">
            <label>Заголовок<input type="text" name="title" required></label>
            <label>Короткий опис<textarea name="summary" required></textarea></label>
            <label>Повний текст<textarea name="content" required></textarea></label>
            <label>Фото (URL)<input type="url" name="image" placeholder="https://..."></label>
            <label>Дата<input type="date" name="date" value="<?= date('Y-m-d') ?>" required></label>
            <button type="submit" class="button">Зберегти</button>
        </form>
    </div>
</section>
