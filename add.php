<?php
require_once('init.php');

if ($con) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $categories = getCategories($con);
        $pageContent = include_template('add_lot.php', ['categories' => $categories, 'connection' => $con, 'errors' => $errors]);
        $layoutContent = include_template('layout.php', [
            'content' => $pageContent,
            'categories' => $categories,
            'isAuth' => $isAuth,
            'userName' => $userName,
            'title' => 'Добавление лота',
            'class' => null,
            'isMain' => false,
            'isAdd' => true
        ]);
        echo $layoutContent;
    } else {
        $lot = $_POST;
        $errors = validateLotForm($lot);
        if (!validateImg($_FILES['image']['tmp_name'])) {
            $extension = pathinfo($_FILES['image']['tmp_name'], PATHINFO_EXTENSION);
            $filename = 'uploads/' . uniqid() . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], $filename);
            $lot['path'] = $filename;
        } else {
            $errors['image'] = validateImg($_FILES['image']['tmp_name']);
        }
        if (empty($errors)) {
            if (insertLotInDb($con, $lot)) {
                $lot_id = mysqli_insert_id($con);
                header("Location: lot.php?lot_id=" . $lot_id);
            } else {
                $error = errorFilter('request', $con);
                $pageContent = include_template('error.php', ['error' => $error]);
                echo $pageContent;
            }
        } else {
            $categories = getCategories($con);
            $pageContent = include_template('add_lot.php', ['categories' => $categories, 'connection' => $con, 'errors' => $errors]);
            $layoutContent = include_template('layout.php', [
                'content' => $pageContent,
                'categories' => $categories,
                'isAuth' => $isAuth,
                'userName' => $userName,
                'title' => 'Добавление лота',
                'class' => null,
                'isMain' => false,
                'isAdd' => true
            ]);
            echo $layoutContent;
        }
    }
} else {
    $error = errorFilter('connect', $con);
    $pageContent = include_template('error.php', ['error' => $error]);
    echo $pageContent;
}




