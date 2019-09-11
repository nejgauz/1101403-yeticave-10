<?php
require_once('init.php');

if ($con) {
    $cards = getCards($con);
    $categories = getCategories($con);
    $items = include_template('items.php', ['cards' => $cards]);
    $pageContent = include_template('main.php', ['items' => $items, 'categories' => $categories]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'isAuth' => $isAuth,
        'userName' => $userName,
        'title' => 'YetiCave - Главная страница',
        'isMain' => true
    ]);
    echo $layoutContent;
} else {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
}







