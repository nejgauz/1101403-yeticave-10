<?php
require_once('init.php');

if (!$con) {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
    exit();
}

$categories = getCategories($con);
$bids = getUserBids($con, $_SESSION['id']);
$pageContent = include_template('my_bets.php', ['categories' => $categories, 'bids' => $bids]);
$layoutContent = include_template('layout.php', [
    'content' => $pageContent,
    'categories' => $categories,
    'title' => 'Мои ставки'
]);
echo $layoutContent;

