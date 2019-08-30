<?php
$isAuth = rand(0, 1);
$userName = 'Анна';

require_once('init.php');

if ($con) {
    $categories = getCategories($con);
    $pageContent = include_template('add_lot.php', ['categories' => $categories, 'connection' => $con]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'isAuth' => $isAuth,
        'userName' => $userName,
        'title' => 'Добавление лота',
        'class' => null,
        'isMain' => false,
        'isAdd' => true
    ]);
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        echo $layoutContent;
    } else {
        $lot = $_POST;

        $filename = 'uploads/' . uniqid() . '.jpg';
        $lot['path'] = $filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $filename);
        if (insertLotInDb($con, $lot)) {
            $lot_id = mysqli_insert_id($con);
            header("Location: lot.php?lot_id=" . $lot_id);
        } else {
            $error = errorFilter('request', $con);
            $pageContent = include_template('error.php', ['error' => $error]);
            echo $pageContent;
        }
    }
} else {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
}




