<?php
require_once('init.php');

if (!$con) {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
    exit();
}
$categories = getCategories($con);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $pageContent = include_template('login_page.php',
        ['categories' => $categories, 'connection' => $con, 'errors' => $errors]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'isAuth' => $isAuth,
        'userName' => $userName,
        'title' => 'Вход'
    ]);
    echo $layoutContent;
    exit();
}

$user = $_POST;
$errors = validateUser($errors, $user);
if (isEmailExist($con, $user['email'])) {
    $regUser = getUserInfo($con, $user['email']);
    $hash = $regUser['password'];
    if (!password_verify($user['password'], $hash)) {
        $errors['password'] = 'Вы ввели неверный пароль';
    }
} else {
    $errors['email'] = 'Адрес почты не зарегистрирован';
}

if (!empty($errors)) {
    $pageContent = include_template('login_page.php',
        ['categories' => $categories, 'connection' => $con, 'errors' => $errors]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'isAuth' => $isAuth,
        'userName' => $userName,
        'title' => 'Вход'
    ]);
    echo $layoutContent;
    exit();
} else {
    $_SESSION['email'] = $regUser['email'];
    $_SESSION['name'] = $regUser['name'];
    header("Location:/");
    exit();
}
