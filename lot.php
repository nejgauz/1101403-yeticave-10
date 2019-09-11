<?php
require_once('init.php');

if (!$con) {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /*
    exit();
    */
}

$categories = getCategories($con);
if (isset($_GET['lot_id']) && is_numeric($_GET['lot_id']) && !empty($card = getCard($con, $_GET['lot_id']))) {
    $pageContent = include_template('lot_card.php', ['card' => $card, 'categories' => $categories, 'connection' => $con]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => $card[0]['name'],
    ]);
    echo $layoutContent;
} else {
    http_response_code(404);
    $pageContent = include_template('http_error.php', ['categories' => $categories, 'error' => '404 Страница не найдена', 'text' => 'Данной страницы не существует на сайте.']);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Ошибка 404'
    ]);
    echo $layoutContent;
}




