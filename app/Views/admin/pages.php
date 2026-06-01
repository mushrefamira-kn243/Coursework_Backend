<section class="admin-module">
    <div class="module-header">
        <div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p>Керуйте статичними сторінками інформаційного блоку сайту.</p>
        </div>
        <div class="admin-actions">
            <a class="button" href="index.php?route=admin">Назад до панелі</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" data-module="pages">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th>Заголовок</th>
                    <th>Контент</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr data-id="<?= intval($item['id']) ?>" data-item='<?= htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                        <td><?= intval($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['slug']) ?></td>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><?= htmlspecialchars(mb_substr($item['content'], 0, 80, 'UTF-8')) ?>...</td>
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
        <h2>Додати / редагувати сторінку</h2>
        <form id="admin-form" data-module="pages">
            <input type="hidden" name="id" value="0">
            <label>URL (slug)<input type="text" name="slug" required placeholder="about-us"></label>
            <label>Заголовок<input type="text" name="title" required></label>
            <label>Контент<textarea name="content" required></textarea></label>
            <button type="submit" class="button">Зберегти</button>
        </form>
    </div>
</section>
