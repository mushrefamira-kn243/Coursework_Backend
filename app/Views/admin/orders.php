<section class="admin-module admin-orders">
    <div class="module-header">
        <div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p>Переглядайте замовлення і змінюйте їх статус.</p>
        </div>
        <div class="admin-actions">
            <a class="button" href="index.php?route=admin">Назад до панелі</a>
        </div>
    </div>

    <div class="table-wrap">
        <table class="data-table" data-module="orders">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Користувач</th>
                    <th>Сума</th>
                    <th>Статус</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr data-id="<?= intval($item['id']) ?>">
                        <td><?= intval($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['user_name'] ?: 'Гість') ?></td>
                        <td><?= htmlspecialchars(number_format((float) $item['total'], 2, '.', '')) ?> ₴</td>
                        <td>
                            <select class="order-status" data-id="<?= intval($item['id']) ?>">
                                <option value="in_process" <?= $item['status'] === 'in_process' ? 'selected' : '' ?>>В процесі</option>
                                <option value="completed" <?= $item['status'] === 'completed' ? 'selected' : '' ?>>Виконано</option>
                                <option value="cancelled" <?= $item['status'] === 'cancelled' ? 'selected' : '' ?>>Відмінено</option>
                            </select>
                        </td>
                        <td><?= htmlspecialchars($item['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
