<?php
require_once('init.php');

$categories = getCategories($con);
if (isset($_GET['category']) && $_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = strip_tags($_GET['category']);
    $allCards = getSearchCategory($con, $id);
    $request = '';
    foreach ($categories as $category) {
        if ($category['id'] === $id) {
            $request = $category['name'];
        }
    }
    if ($allCards) {
        $itemsNumber = count($allCards);
        $curPage = $_GET['page'] ?? 1;
        $itemsLimit = 9;
        $pagesNumber = ceil($itemsNumber / $itemsLimit);
        $offset = ($curPage - 1) * $itemsLimit;
        $cards = getSearchCategory($con, $id, true, $itemsLimit, $offset);
        $link = "search_category.php?category=" . strip_tags($id);
        $items = include_template('items.php', ['cards' => $cards]);
        $pageContent = include_template('search_result.php', [
            'categories' => $categories,
            'connection' => $con,
            'items' => $items,
            'request' => $request,
            'link' => $link,
            'pagesNumber' => $pagesNumber,
            'curPage' => $curPage,
            'header' => 'Все лоты в категории '
        ]);
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'title' => 'Все лоты',
            'request' => $request
        ]);
        echo $layoutContent;
    } elseif ($request === '') {
        $layoutContent = error404($categories);
        echo $layoutContent;
    } else {
        $pageContent = include_template('search_none.php', [
            'text' => 'В данной категории лотов нет',
            'categories' => $categories,
            'request' => $request
        ]);
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'title' => 'Результаты поиска',
            'request' => $request
        ]);
        echo $layoutContent;
    }
}
