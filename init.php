<?php
$isAuth = rand(0, 1);
$userName = 'Анна';
$errors = [];

require_once('functions.php');
require_once('helpers.php');

$con = mysqli_connect('127.0.0.1', 'root', '', 'yeticave');
mysqli_set_charset($con, 'utf8');




