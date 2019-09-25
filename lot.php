<?php
require_once('init.php');

if (!$con) {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
    exit();
}

$categories = getCategories($con);
if (isset($_GET['lot_id']) && is_numeric($_GET['lot_id']) && !empty($card = getCard($con, $_GET['lot_id']))) {
    $card = $card[0];
    $maxBid = getMaxBid($con, $card['id']) ?? 0;
    $curPrice = $card['st_price'];
    $maxPrice = getMaxPrice($curPrice, $maxBid);
    $minBid = $maxPrice + $card['step'];
    $bids = getBids($con, $_GET['lot_id']);
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $pageContent = include_template('lot_card.php', [
            'card' => $card,
            'categories' => $categories,
            'maxPrice' => $maxPrice,
            'minBid' => $minBid,
            'errors' => [],
            'bids' => $bids,
            'con' => $con
        ]);
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'title' => $card['name']
        ]);
        echo $layoutContent;
        exit();
    }
    $bid = $_POST;
    $errors = validateBid($bid, $minBid);
    if (!empty($errors)) {
        $pageContent = include_template('lot_card.php', [
            'card' => $card,
            'categories' => $categories,
            'maxPrice' => $maxPrice,
            'minBid' => $minBid,
            'errors' => $errors,
            'bids' => $bids,
            'con' => $con
        ]);
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'title' => $card['name']
        ]);
        echo $layoutContent;
        exit();
    }
    $bid['user_id'] = $_SESSION['id'];
    $bid['lot_id'] = $_GET['lot_id'];
    if (insertBidInDb($con, $bid)) {
        header("Location: lot.php?lot_id=" . $bid['lot_id']);
        exit();
    }
    $error = errorFilter('request', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Ошибка'
    ]);
    echo $layoutContent;
    exit();
} else {
    http_response_code(404);
    $pageContent = include_template('http_error.php', [
        'categories' => $categories,
        'error' => '404 Страница не найдена',
        'text' => 'Данной страницы не существует на сайте.'
    ]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Ошибка 404'
    ]);
    echo $layoutContent;
}




