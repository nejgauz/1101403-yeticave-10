<?php
require_once('init.php');


$categories = getCategories($con);
if (!isset($_SESSION['name'])) {
    http_response_code(403);
    $pageContent = include_template('http_error.php', [
        'categories' => $categories,
        'error' => '403 Доступ запрещен',
        'text' => 'У вас нет прав для просмотра этой страницы.'
    ]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Ошибка 403'
    ]);
    echo $layoutContent;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $pageContent = include_template('add_lot.php', [
        'categories' => $categories,
        'connection' => $con,
        'errors' => $errors]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Добавление лота',
        'isAdd' => true
    ]);
    echo $layoutContent;
    exit();
}
$lot = $_POST;
$errors = validateLotForm($lot);
if (!validateImg($_FILES['image']['tmp_name']) && empty($errors)) {
    $extension = pathinfo($_FILES['image']['tmp_name'], PATHINFO_EXTENSION);
    $filename = 'uploads/' . uniqid() . $extension;
    move_uploaded_file($_FILES['image']['tmp_name'], $filename);
    $lot['path'] = $filename;
} else {
    $errors['image'] = validateImg($_FILES['image']['tmp_name']);
}

if (empty($errors)) {
    $lot['user_id'] = $_SESSION['id'];

    if (insertLotInDb($con, $lot)) {
        $lot_id = mysqli_insert_id($con);
        header("Location: lot.php?lot_id=" . $lot_id);
        exit();
    }
    $error = errorFilter('request', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Ошибка',
    ]);
    echo $layoutContent;
} else {
    $pageContent = include_template('add_lot.php',
        ['categories' => $categories, 'connection' => $con, 'errors' => $errors]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Добавление лота',
        'isAdd' => true
    ]);
    echo $layoutContent;
}



