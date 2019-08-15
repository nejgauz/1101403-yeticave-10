<?php
$isAuth = rand(0, 1);
$userName = 'Анна';

require_once('functions.php');
require_once('data.php');
require_once('helpers.php');

$pageContent = include_template('main.php', ['cards' => $cards, 'categories' => $categories]);

$layoutContent = include_template('layout.php', [
    'content' => $pageContent,
    'categories' => $categories,
    'isAuth' => $isAuth,
    'userName' => $userName,
    'title' => 'YetiCave - Главная страница'
]);

echo $layoutContent;
