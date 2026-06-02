<section class="auth-card">
    <h1>Увійти</h1>
    <p class="note">Введіть свої дані для входу як користувач або адміністратор.</p>
    <form method="post" action="index.php?route=login" class="form login-form">
        <label>Логін
            <input type="text" name="login" required placeholder="Ваш логін">
        </label>
        <label>Пароль
            <input type="password" name="password" required placeholder="Ваш пароль">
        </label>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <button type="submit" class="button">Увійти</button>
    </form>
    <p class="hint">Немає акаунту? <a href="index.php?route=register">Зареєструватися</a></p>
</section>
