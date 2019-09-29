<?php
require_once('init.php');
require_once('getwinner.php');

if (!$con) {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
    exit();
}

$categories = getCategories($con);
$allCards = getCards($con);$itemsNumber = count($allCards);
$curPage = $_GET['page'] ?? 1;
$itemsLimit = 9;
$pagesNumber = ceil($itemsNumber / $itemsLimit);
$offset = ($curPage - 1) * $itemsLimit;
$cards = getCards($con,true, $itemsLimit, $offset);
$link = "index.php?";
$items = include_template('items.php', ['cards' => $cards]);
$pageContent = include_template('main.php', [
    'items' => $items,
    'categories' => $categories,
    'pagesNumber' => $pagesNumber,
    'curPage' => $curPage,
    'link' => $link
]);
$layoutContent = include_template('layout.php', [
    'content' => $pageContent,
    'categories' => $categories,
    'title' => 'YetiCave - Главная страница',
    'isMain' => true
]);
echo $layoutContent;








