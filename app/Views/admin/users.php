<section class="admin-module">
    <div class="module-header">
        <div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p>Керуйте клієнтськими акаунтами, ролями та контактною інформацією.</p>
        </div>
        <div class="admin-actions">
            <a class="button" href="index.php?route=admin">Назад до панелі</a>
        </div>
    </div>
    <div class="admin-search-row">
        <input id="user-search" type="search" placeholder="Шукати користувача за ім'ям, логіном або email...">
    </div>

    <div class="table-wrap">
        <table class="data-table" data-module="users">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Логін</th>
                    <th>Ім'я</th>
                    <th>Електронна пошта</th>
                    <th>Роль</th>
                    <th>Дата реєстрації</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <?php $itemData = $item; unset($itemData['password']); ?>
                    <tr data-id="<?= intval($item['id']) ?>" data-item='<?= htmlspecialchars(json_encode($itemData, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                        <td><?= intval($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['login'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['email']) ?></td>
                        <td><?= htmlspecialchars($item['role']) ?></td>
                        <td><?= htmlspecialchars($item['registered']) ?></td>
                        <td>
                            <button class="button button-small" data-action="edit">Редагувати</button>
                            <?php if (($item['role'] ?? '') !== 'admin'): ?>
                                <button class="button button-small button-secondary" data-action="delete">Видалити</button>
                            <?php else: ?>
                                <button class="button button-small button-secondary" disabled>Захищено</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-card">
        <h2>Додати / редагувати користувача</h2>
        <form id="admin-form" data-module="users">
            <input type="hidden" name="id" value="0">
            <label>Логін<input type="text" name="login" required></label>
            <label>Ім'я<input type="text" name="name" required></label>
            <label>Електронна пошта<input type="email" name="email" required></label>
            <label>Пароль<input type="password" name="password" placeholder="Залиште порожнім для збереження старого"></label>
            <label>Роль<select name="role">
                <option value="user">Користувач</option>
                <option value="admin">Адміністратор</option>
            </select></label>
            <label>Дата реєстрації<input type="date" name="registered" value="<?= date('Y-m-d') ?>" required></label>
            <button type="submit" class="button">Зберегти</button>
        </form>
    </div>
</section>
