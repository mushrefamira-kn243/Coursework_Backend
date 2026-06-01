<section class="admin-page">
    <aside class="admin-sidebar">
        <div class="admin-user">
            <span>Увійшов як</span>
            <strong><?= htmlspecialchars(Auth::getUserName() ?? 'Гість') ?></strong>
        </div>
        <nav class="admin-nav">
            <a class="admin-nav-link" href="index.php?route=admin">Головна</a>
            <a class="admin-nav-link active" href="index.php?route=admin_analytics">Аналітика продажів</a>
            <a class="admin-nav-link" href="index.php?route=module&name=products">Товари</a>
            <a class="admin-nav-link" href="index.php?route=module&name=orders">Замовлення</a>
            <a class="admin-nav-link" href="index.php?route=module&name=news">Новини</a>
            <a class="admin-nav-link" href="index.php?route=module&name=gallery">Галерея</a>
            <a class="admin-nav-link" href="index.php?route=module&name=pages">Сторінки</a>
            <a class="admin-nav-link" href="index.php?route=module&name=users">Користувачі</a>
        </nav>
    </aside>
    <div class="admin-main">
        <section class="admin-analytics">
            <h1>Аналітика продажів</h1>
            <div class="analytics-actions">
                <a class="button button-secondary" href="index.php?route=admin">Повернутися до панелі</a>
            </div>
    <section class="analytics-block">
        <h2>Продажі за день</h2>
        <table class="analytics-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Замовлень</th>
                    <th>Дохід</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dailySales)): ?>
                    <?php foreach ($dailySales as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['day']) ?></td>
                            <td><?= intval($row['orders']) ?></td>
                            <td><?= number_format(floatval($row['revenue']), 2, '.', ' ') ?> ₴</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">Дані відсутні</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
    <section class="analytics-block">
        <h2>Найпопулярніші товари</h2>
        <table class="analytics-table">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Продано</th>
                    <th>Дохід</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($topProducts)): ?>
                    <?php foreach ($topProducts as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= intval($item['sold_qty']) ?></td>
                            <td><?= number_format(floatval($item['revenue']), 2, '.', ' ') ?> ₴</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">Дані відсутні</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
    <section class="analytics-block">
        <h2>Список замовлень</h2>
        <table class="analytics-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Користувач</th>
                    <th>Сума</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= intval($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['user_name'] ?: 'Гість') ?></td>
                            <td><?= number_format(floatval($order['total']), 2, '.', ' ') ?> ₴</td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">Дані відсутні</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
    </div>
</section>
