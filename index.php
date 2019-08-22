<?php
$isAuth = rand(0, 1);
$userName = 'Анна';

require_once('functions.php');
require_once('helpers.php');

$con = getConnect();
if (is_string($con)) {
    $pageContent = include_template('error.php', ['error' => $con]);
    echo $pageContent;
} else {
    $cards = getCards($con);
    $categories = getCategories($con);
    if (is_array($cards)  && is_array($categories)) {
        $pageContent = include_template('main.php', ['cards' => $cards, 'categories' => $categories]);
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'isAuth' => $isAuth,
            'userName' => $userName,
            'title' => 'YetiCave - Главная страница'
        ]);
        echo $layoutContent;
    } else {
        if (is_string($cards)) {
            $pageContent = include_template('error.php', ['error' => $cards]);
            echo $pageContent;
        } elseif (is_string($categories)) {
            $pageContent = include_template('error.php', ['error' => $categories]);
            echo $pageContent;
        }
    }
}





