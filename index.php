<?php
$isAuth = rand(0, 1);
$userName = 'Анна';

require_once('functions.php');
require_once('data.php');

$pageContent = includeTemplate('main.php', ['cards' => $cards, 'categories' => $categories]);

$layoutContent = includeTemplate('layout.php', [
    'content' => $pageContent,
    'categories' => $categories,
    'isAuth' => $isAuth,
    'userName' => $userName,
    'title' => 'YetiCave - Главная страница'
]);

echo $layoutContent;
