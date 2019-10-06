<?php
require_once('init.php');

$categories = getCategories($con);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $layoutContent = loginPage($categories, $con, $errors);
    echo $layoutContent;
    exit();
}

$user = $_POST;
foreach ($user as $key => $value) {
    $user[$key] = strip_tags($value);
}
if (isset($user['email']) && isset($user['password'])) {
    if (isEmailExist($con, $user['email'])) {
        $regUser = getUserInfo($con, $user['email']);
        $hash = $regUser['password'] ?? '';
        if (isset($user['password']) && !password_verify($user['password'], $hash)) {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    } else {
        $errors['email'] = 'Вы ввели неверный email';
    }
    $errors = validateUser($errors, $user);
} else {
    $errors['email'] = 'Это поле должно быть заполнено';
    $errors['password'] = 'Это поле должно быть заполнено';
}

if (!empty($errors)) {
    $layoutContent = loginPage($categories, $con, $errors);
    echo $layoutContent;
    exit();
}
$_SESSION['id'] = $regUser['id'];
$_SESSION['email'] = $regUser['email'];
$_SESSION['name'] = $regUser['name'];
header("Location:/");
exit();




