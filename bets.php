<?php
require_once('init.php');

$categories = getCategories($con);
if (isset($_SESSION['id'])) {
    $bids = getUserBids($con, $_SESSION['id']);
    $pageContent = include_template('my_bets.php', ['categories' => $categories, 'bids' => $bids]);
    $layoutContent = include_template('layout.php', [
        'content' => $pageContent,
        'categories' => $categories,
        'title' => 'Мои ставки'
    ]);
    echo $layoutContent;
    exit;
}

http_response_code(403);
$pageContent = include_template('http_error.php', [
    'categories' => $categories,
    'error' => '403 Доступ запрещен',
    'text' => 'Чтобы попасть на эту страницу, войдите в свою учетную запись'
]);
$layoutContent = include_template('layout.php', [
    'content' => $pageContent,
    'categories' => $categories,
    'title' => 'Ошибка 403'
]);
echo $layoutContent;
exit();


