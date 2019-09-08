<?php
require_once('init.php');

if (isset($_SESSION['name'])) {
    http_response_code(403);
    exit();
}

if (!$con) {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $categories = getCategories($con);
    $pageContent = include_template('sign_up_page.php',
        ['categories' => $categories, 'connection' => $con, 'errors' => $errors]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Регистрация',
        'isSign' => true
    ]);
    echo $layoutContent;
    exit();
}

$user = $_POST;
if ($user['password']) {
    $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
}
$errors = validateUser($errors, $user);
if (isEmailExist($con, $user['email'])) {
    $errors['email'] = 'Пользователь с таким паролем уже существует';
}
if (!empty($errors)) {
    $categories = getCategories($con);
    $pageContent = include_template('sign_up_page.php',
        ['categories' => $categories, 'connection' => $con, 'errors' => $errors]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Регистрация',
        'isSign' => true
    ]);
    echo $layoutContent;
    exit();
} else {
    if (insertUserInDb($con, $user)) {
        header("Location:/");
        exit();
    } else {
        $error = errorFilter('request', $con);
        $pageContent = include_template('error.php', ['error' => $error]);
        echo $pageContent;
    }
}

