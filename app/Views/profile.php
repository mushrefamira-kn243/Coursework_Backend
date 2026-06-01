<section class="profile-page">
    <div class="profile-card">
        <h1>Мій профіль</h1>
        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <div class="profile-layout">
            <div class="profile-avatar-card">
                <div class="avatar-preview" style="background-image:url('<?= htmlspecialchars($user['avatar'] ?: 'https://via.placeholder.com/240x240?text=Avatar') ?>')"></div>
                <p>Завантажте ваше фото для профілю.</p>
            </div>
            <form class="profile-form" method="post" enctype="multipart/form-data">
                <label>Логін<input type="text" value="<?= htmlspecialchars($user['login'] ?? '') ?>" disabled></label>
                <label>Ім'я<input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required></label>
                <label>Електронна пошта<input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required></label>
                <label>Пароль<input type="password" name="password" placeholder="Залиште порожнім для збереження пароля"></label>
                <label>Фото профілю<input type="file" name="avatar" accept="image/png,image/jpeg,image/gif"></label>
                <button type="submit" class="button">Оновити профіль</button>
            </form>
        </div>
    </div>
</section>
