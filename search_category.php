<?php
require_once('init.php');

if (!$con) {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
    exit();
}

$categories = getCategories($con);
if (isset($_GET['category']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $request = strip_tags($_GET['category']);
    $allCards = getSearchResults($con, $request, '`name`');
    if ($allCards) {
        $itemsNumber = count($allCards);
        $curPage = $_GET['page'] ?? 1;
        $itemsLimit = 9;
        $pagesNumber = ceil($itemsNumber/$itemsLimit);
        $offset = ($curPage - 1) * $itemsLimit;
        $cards = getSearchResults($con, $request, '`name`', $itemsLimit, $offset);
        $items = include_template('items.php', ['cards' => $cards]);
        $pageContent = include_template('search_result.php', [
            'categories' => $categories,
            'connection' => $con,
            'items' => $items,
            'request' => $request,
            'pagesNumber' => $pagesNumber,
            'curPage' => $curPage,
            'header' => 'Все лоты в категории '
        ]);
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'title' => 'Все лоты'
        ]);
        echo $layoutContent;
    } else {
        $pageContent = include_template('search_none.php', ['text' => 'В данной категории лотов нет']);
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'title' => 'Результаты поиска'
        ]);
        echo $layoutContent;
    }

}
