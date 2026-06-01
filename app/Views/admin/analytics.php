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
            <a class="admin-nav-link" href="index.php?route=module&name=users">Користувачі</a>
        </nav>
    </aside>
    <div class="admin-main">
        <section class="admin-analytics">
            <h1>Аналітика продажів</h1>
            <div class="analytics-actions">
                <a class="button button-secondary" href="index.php?route=admin">Повернутися до панелі</a>
            </div>
            <form class="analytics-filters" method="get" action="index.php?route=admin_analytics">
                <input type="hidden" name="route" value="admin_analytics">
                <label>
                    Період
                    <select name="period">
                        <option value="3" <?= $filters['period'] === '3' ? 'selected' : '' ?>>Останні 3 дні</option>
                        <option value="7" <?= $filters['period'] === '7' ? 'selected' : '' ?>>Останні 7 днів</option>
                        <option value="30" <?= $filters['period'] === '30' ? 'selected' : '' ?>>Останні 30 днів</option>
                        <option value="month" <?= $filters['period'] === 'month' ? 'selected' : '' ?>>Поточний місяць</option>
                    </select>
                </label>
                <label>
                    Категорія
                    <select name="category">
                        <option value="">Усі</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $filters['category'] === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Ціна від
                    <input type="number" step="0.01" name="min_price" value="<?= htmlspecialchars($filters['min_price']) ?>" placeholder="0">
                </label>
                <label>
                    до
                    <input type="number" step="0.01" name="max_price" value="<?= htmlspecialchars($filters['max_price']) ?>" placeholder="0">
                </label>
                <button class="button" type="submit">Застосувати</button>
            </form>
            <div class="analytics-summary">
                <?php $totalOrders = count($orders); ?>
                <?php $totalRevenue = array_sum(array_map(function ($item) { return floatval($item['total']); }, $orders)); ?>
                <div class="analytics-card">
                    <span>Замовлень</span>
                    <strong><?= intval($totalOrders) ?></strong>
                </div>
                <div class="analytics-card">
                    <span>Дохід</span>
                    <strong><?= number_format($totalRevenue, 2, '.', ' ') ?> ₴</strong>
                </div>
                <div class="analytics-card">
                    <span>Топ товарів</span>
                    <strong><?= intval(count($topProducts)) ?></strong>
                </div>
            </div>
            <section class="analytics-block">
                <h2>Тренд продажів</h2>
                <div class="analytics-chart">
                    <?php
                        $maxRevenue = 0;
                        foreach ($dailySales as $row) {
                            $maxRevenue = max($maxRevenue, floatval($row['revenue']));
                        }
                    ?>
                    <?php if (!empty($dailySales)): ?>
                        <?php foreach (array_reverse($dailySales) as $row): ?>
                            <?php $value = floatval($row['revenue']); ?>
                            <?php $width = $maxRevenue > 0 ? ($value / $maxRevenue) * 100 : 0; ?>
                            <div class="analytics-bar-row">
                                <span class="analytics-bar-label"><?= htmlspecialchars($row['day']) ?></span>
                                <div class="analytics-bar-track">
                                    <div class="analytics-bar-fill" style="width: <?= round($width, 1) ?>%;"></div>
                                </div>
                                <span class="analytics-bar-value"><?= number_format($value, 2, '.', ' ') ?> ₴</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="analytics-empty">Дані відсутні для побудови графіку</div>
                    <?php endif; ?>
                </div>
            </section>
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
        <h2>Топ категорій</h2>
        <table class="analytics-table">
            <thead>
                <tr>
                    <th>Категорія</th>
                    <th>Одиниць</th>
                    <th>Дохід</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($topCategories)): ?>
                    <?php foreach ($topCategories as $categoryRow): ?>
                        <tr>
                            <td><?= htmlspecialchars($categoryRow['category']) ?></td>
                            <td><?= intval($categoryRow['units']) ?></td>
                            <td><?= number_format(floatval($categoryRow['revenue']), 2, '.', ' ') ?> ₴</td>
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
