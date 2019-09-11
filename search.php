<?php
require_once('init.php');

if (!$con) {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
    exit();
}

$categories = getCategories($con);
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $request = trim($_GET['search']);
    if (!empty($request)) {
        $allCards = getSearchResults($con, $request);
        if ($allCards) {
            $itemsNumber = count($allCards);
            $curPage = $_GET['page'] ?? 1;
            $itemsLimit = 9;
            $pagesNumber = ceil($itemsNumber/$itemsLimit);
            $offset = ($curPage - 1) * $itemsLimit;
            $cards = getSearchResults($con, $request, $itemsLimit, $offset);
            $items = include_template('items.php', ['cards' => $cards]);
            $pageContent = include_template('search_result.php', [
                'categories' => $categories,
                'connection' => $con,
                'items' => $items,
                'request' => $request,
                'pagesNumber' => $pagesNumber,
                'curPage' => $curPage
            ]);
            $layoutContent = include_template('layout.php', [
                'content' => $pageContent,
                'categories' => $categories,
                'title' => 'Результаты поиска'
            ]);
            echo $layoutContent;
        } else {
            $pageContent = include_template('search_none.php');
            $layoutContent = include_template('layout.php', [
                'content' => $pageContent,
                'categories' => $categories,
                'title' => 'Результаты поиска'
            ]);
            echo $layoutContent;
        }
    } else {
        $pageContent = include_template('search_none.php');
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'title' => 'Результаты поиска'
        ]);
        echo $layoutContent;
    }
} else {
    http_response_code(403);
    exit();
}
