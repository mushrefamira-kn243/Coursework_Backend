<section class="auth-card">
    <?php $formData = $data ?? []; ?>
    <h1>Реєстрація</h1>
    <p class="note">Створіть акаунт, щоб отримати доступ до сайту.</p>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" action="index.php?route=register" class="form login-form">
        <label>Логін
            <input type="text" name="login" required placeholder="Ваш логін" value="<?= htmlspecialchars($formData['login'] ?? '') ?>">
        </label>
        <label>Ім'я
            <input type="text" name="name" required placeholder="Ваше ім'я" value="<?= htmlspecialchars($formData['name'] ?? '') ?>">
        </label>
        <label>Електронна пошта
            <input type="email" name="email" required placeholder="email@example.com" value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
        </label>
        <label>Пароль
            <input type="password" name="password" required placeholder="Пароль">
        </label>
        <label>Підтвердження пароля
            <input type="password" name="confirm_password" required placeholder="Підтвердіть пароль">
        </label>
        <button type="submit" class="button">Зареєструватися</button>
    </form>
    <p class="hint">Вже є акаунт? <a href="index.php?route=login">Увійти</a></p>
</section>
