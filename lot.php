<?php
$isAuth = rand(0, 1);
$userName = 'Анна';

require_once('init.php');

if ($con) {
    if (isset($_GET['lot_id']) && is_numeric($_GET['lot_id']) && isIdExist($con)) {
        $card = getCard($con, $_GET['lot_id']);
        $categories = getCategories($con);
        $pageContent = include_template('lot_card.php', ['card' => $card, 'categories' => $categories, 'connection' => $con]);
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'isAuth' => $isAuth,
            'userName' => $userName,
            'title' => $card[0]['name'],
            'class' => null
        ]);
        echo $layoutContent;
    } else {
        http_response_code(404);
        exit('404 NOT FOUND');
    }
} else {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
}

