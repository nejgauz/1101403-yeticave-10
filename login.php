<?php
require_once('init.php');

$categories = getCategories($con);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $layoutContent = loginPage($categories, $con, $errors);
    echo $layoutContent;
    exit();
}

$user = $_POST;
if (!empty($user)) {
    foreach ($user as $key => $value) {
        $user[$key] = strip_tags($value);
    }
    if (isEmailExist($con, $user['email'])) {
        $regUser = getUserInfo($con, $user['email']);
        $hash = $regUser['password'] ?? '';
        if (!password_verify($user['password'], $hash)) {
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    } else {
        $errors['email'] = 'Вы ввели неверный email';
    }
    $errors = validateUser($errors, $user);

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
}

http_response_code(404);
$layoutContent = error404($categories);
echo $layoutContent;
exit();


