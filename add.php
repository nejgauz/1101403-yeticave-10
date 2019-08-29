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
    echo $layoutContent;
} else {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
}




