<?php
require_once('init.php');
require_once('vendor/autoload.php');

if ($con) {
    $lots = getLotsWithoutWinner($con);
    foreach ($lots as $lot) {
        $winner = getLastBid($con, $lot['id'] ?? '');
        if ($winner) {
            insertWinnerInDB($con, $lot['id'] ?? '', $winner);
            $winData = getWinData($con, $winner);
            sendWinEmail($winData);
        }
    }
}


