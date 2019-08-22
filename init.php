<?php
$isAuth = rand(0, 1);
$userName = 'Анна';

require_once('functions.php');
require_once('helpers.php');

$connection = mysqli_connect('127.0.0.1', 'root', '', 'yeticave');
if (!$connection) {
    errorFilter('connect', null);
} else {

}



