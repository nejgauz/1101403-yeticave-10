<?php
$isAuth = rand(0, 1);
$userName = 'Анна';

require_once('init.php');

if ($con) {
    $cards = getCards($con);
    $categories = getCategories($con);
    $pageContent = include_template('main.php', ['cards' => $cards, 'categories' => $categories]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'isAuth' => $isAuth,
        'userName' => $userName,
        'title' => 'YetiCave - Главная страница',
        'class' => "container",
        'isMain' => true
    ]);
    echo $layoutContent;
} else {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
}







