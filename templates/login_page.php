<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $value): ?>
            <li class="nav__item">
                <a href="search_category.php?category=<?php echo $value['id'] ?? ''; ?>"><?php echo htmlspecialchars($value['name']) ?? ''; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<form class="form container <?php if (!empty($errors)): echo 'form--invalid'; endif; ?>" action="login.php"
      method="post">
    <h2>Вход</h2>
    <div class="form__item <?php echo errorClass($errors, 'email'); ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?php echo htmlspecialchars(getPostVal('email')); ?>">
        <span class="form__error"><?php echo $errors['email'] ?? ''; ?></span>
    </div>
    <div class="form__item form__item--last <?php echo errorClass($errors, 'password'); ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль"
               value="<?php echo htmlspecialchars(getPostVal('password')); ?>">
        <span class="form__error"><?php echo $errors['password'] ?? ''; ?></span>
    </div>
    <button type="submit" class="button">Войти</button>
</form>
