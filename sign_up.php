<?php
require_once('init.php');

if (isset($_SESSION['name'])) {
    http_response_code(403);
    exit();
}

$categories = getCategories($con);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $layoutContent = entrancePage($categories, $con, $errors);
    echo $layoutContent;
    exit();
}

$user = $_POST;
if (!isset($user['password']) or !isset($user['email'])) {
    http_response_code(404);
    $layoutContent = error404($categories);
    echo $layoutContent;
    exit();
}

foreach ($user as $key => $value) {
    $user[$key] = strip_tags($value);
}
if (!empty($user['password'])) {
    $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
}
$errors = validateUser($errors, $user);
if (isset($user['email']) && isEmailExist($con, $user['email'])) {
    $errors['email'] = 'Этот адрес почты уже занят';
}

if (!empty($errors)) {
    $layoutContent = entrancePage($categories, $con, $errors);
    echo $layoutContent;
    exit();
}

if (insertUserInDb($con, $user)) {
    header("Location:/");
    exit();
}

$error = errorFilter('request', $con);
$pageContent = include_template('error.php', ['error' => $error]);
echo $pageContent;



