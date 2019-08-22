<?php
require_once('init.php');

$sql1 = "SELECT title AS name, st_price AS price, image_path AS url, c.name AS category, dt_end AS 'time'
FROM lots AS l
LEFT JOIN categories AS c
ON c.id = l.cat_id
WHERE win_id IS NULL AND dt_end > NOW()
ORDER BY l.dt_create DESC LIMIT 9";
$cards = readFromDatabase($sql1, $connection);

$sql2 = "SELECT name FROM categories";
$categories = readFromDatabase($sql2, $connection);

$pageContent = include_template('main.php', ['cards' => $cards, 'categories' => $categories]);

$layoutContent = include_template('layout.php', [
    'content' => $pageContent,
    'categories' => $categories,
    'isAuth' => $isAuth,
    'userName' => $userName,
    'title' => 'YetiCave - Главная страница'
]);

echo $layoutContent;
