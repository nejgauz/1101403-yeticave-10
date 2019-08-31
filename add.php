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
        $requiredFields = $lot;
        foreach ($requiredFields as $field => $value) {
            if (isFieldEmpty($field)) {
                $errors[$field] = isFieldEmpty($field);
                unset($requiredFields[$field]);
            }
        }
        if (!empty($_FILES['image']['tmp_name'])) {
            $extension = pathinfo($_FILES['image']['tmp_name'], PATHINFO_EXTENSION);
            $filename = 'uploads/' . uniqid() . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], $filename);
            if (validateImg($filename)) {
                $errors['image'] = validateImg($filename);
                unlink($filename);
            } else {
                $lot['path'] = $filename;
            }
        } else {
            $errors['image'] = 'Изображение не выбрано';
        }
        $rules = [
            'lot-rate' => function () {
                return validatePrice('lot-rate');
            },
            'lot-date' => function () {
                return validateData('lot-date');
            },
            'lot-step' => function () {
                return validateStep('lot-step');
            }
        ];
        foreach ($requiredFields as $field => $value) {
            if (isset($rules[$field])) {
                $rule = $rules[$field];
                $errors[$field] = $rule();
            }
        }
        $errors = array_filter($errors);
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




