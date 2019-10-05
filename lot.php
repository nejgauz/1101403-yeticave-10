<?php
require_once('init.php');

$categories = getCategories($con);
if (isset($_GET['lot_id']) && is_numeric($_GET['lot_id']) && !empty($card = getCard($con, $_GET['lot_id']))) {
    $card = $card[0];
    $maxBid = getMaxBid($con, $card['id']) ?? 0;
    $curPrice = $card['st_price'] ?? 0;
    $maxPrice = max($curPrice, $maxBid);
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
            'title' => $card['name'] ?? ''
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
            'title' => $card['name'] ?? ''
        ]);
        echo $layoutContent;
        exit();
    }
    $bid['user_id'] = $_SESSION['id'];
    $bid['lot_id'] = $_GET['lot_id'];
    foreach ($bid as $key => $value) {
        $bid[$key] = strip_tags($value);
    }
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
    $layoutContent = error404($categories);
    echo $layoutContent;
    exit();
}




