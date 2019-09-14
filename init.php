<?php
$errors = [];
session_start();

require_once('functions.php');
require_once('helpers.php');

$con = mysqli_connect('127.0.0.1', 'root', '', 'yeticave');
mysqli_set_charset($con, 'utf8');
