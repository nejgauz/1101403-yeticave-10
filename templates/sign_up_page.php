<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $value): ?>
            <li class="nav__item">
                <a href="search_category.php?category=<?php echo $value['id'] ?? ''; ?>"><?php echo htmlspecialchars($value['name']) ?? ''; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<form class="form container <?php if (!empty($errors)): echo 'form--invalid'; endif; ?>" action="sign_up.php"
      method="post" autocomplete="off">
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?php echo errorClass($errors, 'email'); ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?php echo htmlspecialchars(getPostVal('email')); ?>">
        <span class="form__error"><?php echo $errors['email'] ?? ''; ?></span>
    </div>
    <div class="form__item <?php echo errorClass($errors, 'password'); ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль"
               value="<?php echo htmlspecialchars(getPostVal('password')); ?>">
        <span class="form__error"><?php echo $errors['password'] ?? ''; ?></span>
    </div>
    <div class="form__item <?php echo errorClass($errors, 'name'); ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?php echo htmlspecialchars(getPostVal('name')); ?>">
        <span class="form__error"><?php echo $errors['name'] ?? ''; ?></span>
    </div>
    <div class="form__item <?php echo errorClass($errors, 'message'); ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message"
                  placeholder="Напишите как с вами связаться"><?php echo htmlspecialchars(getPostVal('message')); ?></textarea>
        <span class="form__error"><?php echo $errors['message'] ?? ''; ?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="login.php">Уже есть аккаунт</a>
</form>
