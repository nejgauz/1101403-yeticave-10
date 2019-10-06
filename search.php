<?php
require_once('init.php');

$categories = getCategories($con);
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $request = trim($_GET['search'] ?? '');
    if (!empty($request)) {
        $allCards = getSearchResults($con, $request, 'title, descr');
        if ($allCards) {
            $itemsNumber = count($allCards);
            $curPage = $_GET['page'] ?? 1;
            $itemsLimit = 9;
            $pagesNumber = ceil($itemsNumber / $itemsLimit);
            if (!is_numeric($curPage) or $curPage > $pagesNumber or $curPage < 0) {
                http_response_code(404);
                $layoutContent = error404($categories);
                echo $layoutContent;
                exit();
            }
            $offset = ($curPage - 1) * $itemsLimit;
            $cards = getSearchResults($con, $request, 'title, descr', true, $itemsLimit, $offset);
            $link = "search.php?search=" . strip_tags($request);
            $items = include_template('items.php', ['cards' => $cards]);
            $pageContent = include_template('search_result.php', [
                'categories' => $categories,
                'connection' => $con,
                'items' => $items,
                'request' => $request,
                'link' => $link,
                'pagesNumber' => $pagesNumber,
                'curPage' => $curPage,
                'header' => 'Результаты поиска по запросу '
            ]);
            $layoutContent = include_template('layout.php', [
                'content' => $pageContent,
                'categories' => $categories,
                'title' => 'Результаты поиска'
            ]);
            echo $layoutContent;
        } else {
            $layoutContent = searchNone($categories);
            echo $layoutContent;
        }
    } else {
        $layoutContent = searchNone($categories);
        echo $layoutContent;
    }
} else {
    http_response_code(403);
    exit();
}
