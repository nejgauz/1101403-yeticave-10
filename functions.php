<?php

/**
 * Принимает цену, форматирует и возвращает в нужном формате.
 * @param float $price цена в виде дробного или целого числа
 * @return string отформатированная цена в виде "15 000 ₽"
 */
function priceFormat(float $price): string
{
    $resultPrice = ceil($price);
    $resultPrice = number_format($resultPrice, 0, '.', ' ');
    $resultPrice .= ' ₽';

    return $resultPrice;
}

/**
 * Принимает дату в будущем в формате "ГГГГ-ММ-ДД" и возвращает время до этой даты в виде ассициативного массива
 * @param string $endDate будущая дата в формате "ГГГГ-ММ-ДД"
 * @return array массив вида ['ЧЧ','ММ']
 */
function timeCounter(string $endDate): array
{
    date_default_timezone_set('Europe/Moscow');

    $dateNow = date_create('now');
    $dateFuture = date_create($endDate);
    $timeRest = date_diff($dateFuture, $dateNow);
    $hoursRest = date_interval_format($timeRest, '%H');
    $minRest = date_interval_format($timeRest, '%I');
    $daysRest = date_interval_format($timeRest, '%D');

    return ['hours' => $hoursRest, 'mins' => $minRest, 'days' => $daysRest];

}

/**
 * Обрабатывает ошибки подключения к БД и возвращает страницу с ее типом
 * @param string $mistake вид ошибки: 'connect' (ошибка подключения) или 'request' (ошибка запроса)
 * @param $con ресурс соединения
 * @return шаблон страницы с ошибкой
 */
function errorFilter(string $mistake, $con = null)
{
    if ($mistake === 'connect') {
        $error = mysqli_connect_error();
    } else if ($mistake === 'request') {
        $error = mysqli_error($con);
    }
    return $error;
}

/**
 * Обрабатывает запросы на чтение из БД
 * @param string $sql текст запроса к БД
 * @param $con ресурс соединения
 * @return array двумерный ассоциативный массив с результатами запроса к БД
 */
function readFromDatabase(string $sql, $con): array
{
    $data = mysqli_query($con, $sql);
    $data = mysqli_fetch_all($data, MYSQLI_ASSOC);

    return $data;
}

/**
 * Возвращает список категорий по запросу
 * @param $connection ресурс соединения
 * @return array возвращает двумерный массив категорий
 */
function getCategories($connection): array
{
    $request = "SELECT * FROM categories";
    $categories = readFromDatabase($request, $connection);

    return $categories;
}

/**
 * Возвращает список лотов по запросу
 * @param $connection ресурс соединения
 * @return array возвращает двумерный массив лотов
 */
function getCards($connection): array
{
    $request = "SELECT l.id, title, st_price, image_path, dt_end, c.name AS category_name 
    FROM lots AS l
    LEFT JOIN categories AS c
    ON c.id = l.cat_id
    WHERE win_id IS NULL AND dt_end > NOW()
    ORDER BY l.dt_create DESC LIMIT 9";
    $cards = readFromDatabase($request, $connection);

    return $cards;
}


/**
 * Возвращает данные по id запрашиваемого лота
 * @param $id - id необходимого лота
 * @param $connection ресурс соединения
 * @return array ассоциативный массив с данными лота
 */
function getCard($connection, $id): array
{
    $request = "SELECT l.id, title AS `name`, image_path AS url, c.name AS category, descr AS description, dt_end AS `time`, step, st_price
    FROM lots AS l
    LEFT JOIN categories AS c ON c.id = l.cat_id
    WHERE l.id = " . $id;
    $card = readFromDatabase($request, $connection);

    return $card;
}

/**
 * Возвращает максимальную ставку, если она есть
 * @param $id - id необходимого лота
 * @param $connection ресурс соединения
 * @return возвращает массив с максимальной ставкой
 */
function getMaxBid($connection, $id): array
{
    $request = "SELECT MAX(price) as max_price FROM bids WHERE lot_id = " . $id;
    $bidArray = mysqli_query($connection, $request);
    $maxBid = mysqli_fetch_assoc($bidArray);

    return $maxBid;
}

/**
 * Добавляет новый лот в БД
 * @param $connection ресурс соединения
 * @param array $lot массив с данными лота
 */
function insertLotInDb($connection, array $lot)
{
    $request = "INSERT INTO lots (user_id, dt_create, cat_id, title, descr, image_path, st_price, dt_end, step)
    VALUES (1, NOW(), '" . $lot['category'] . "', '" . $lot['lot-name'] . "', '" . $lot['message'] . "', '" . $lot['path'] . "', '" .  $lot['lot-rate'] . "', '" . $lot['lot-date'] . "', '" . $lot['lot-step'] . "')";
    $result = mysqli_query($connection, $request);

    return $result;
}

/**
 * Подставляет в форму значения, которые уже были заполнены юзером
 * @param $name имя поля в форме
 * @return возвращает заполненные значения либо пустые строки, если поля не были заполнены
 */
function getPostVal(string $name): string
{
    return $_POST[$name] ?? "";
}

/**
 * Проверяет, заполнено ли поле
 * @param $field имя поля, которое нужно проверить на заполненность
 * @return string $result возвращает текст ошибки или пустую строку
 */
function isFieldEmpty($field): string
{
    if (empty($field) or $field == 'Выберите категорию') {
        $result = 'Поле необходимо заполнить';
    } else {
        $result = '';
    }
    return $result;
}

/**
 * Проверяет, правильно ли указана цена
 * @param $price цена лота
 * @return string $result возвращает текст ошибки или пустую строку
 */
function validatePrice($price): string
{
    if (empty($price) or $price < 0) {
        $result = 'Цена должна быть больше нуля';
    } elseif (!floatval($price)) {
        $result = 'Цена должна быть числом';
    } else {
        $result = '';
    }

    return $result;
}

/**
 * Проверяет, является ли тип файла подходящим для формы лота
 * @param $path путь к файлу
 * @return string $result возвращает текст ошибки или пустую строку
 */
function validateImg($path): string
{
    if (!empty($path)) {
        if (mime_content_type($path) !='image/png' && mime_content_type($path) !='image/jpeg') {
            $result = 'Загрузите, пожалуйста, файл в формате jpg, jpeg или png';
        } else {
            $result = '';
        }
    } else {
        $result = 'Изображение не выбрано';
    }

    return $result;
}

/**
 * Проверяет формат даты и что она не в прошлом
 * @param $name название поля даты
 * @return string $result возвращает текст ошибки или пустую строку
 */
function validateData($date): string
{
    if (!is_date_valid($date)) {
        $result = 'Неправильный формат даты. Введите в формате \'ГГГГ-ММ-ДД\'';
    } elseif (strtotime($date) < strtotime('+1 day')) {
        $result = 'Дата окончания торгов должна быть больше текущей хотя бы на один день';
    } else {
        $result = '';
    }
    return $result;
}

/**
 * Проверяет, чтобы шаг ставки был больше нуля и целое число
 * @step название поля шага ставки
 * @return string $result возвращает текст ошибки или пустую строку
 */
function validateStep($step): string
{
    if (empty($step) or $step < 0) {
        $result = 'Шаг ставки должен быть больше нуля';
    } elseif (!intval($step)) {
        $result = 'Шаг ставки должен быть целым числом';
    } else {
        $result = '';
    }
    return $result;
}

/**
 * Возвращает строку с классом ошибки, если ошибки есть в массиве
 * @param array $errors массив с ошибками
 * @param string $name название тега, по которому нужно искать ошибки
 */
function errorClass(array $errors, string $name) {
    if (isset($errors[$name])) {
        echo 'form__item--invalid';
    } else {
        echo '';
    }
}

/**
 * Функция проводит необходимые проверки для валидации формы добавления лота
 * @param array $lot массив с данными из отправленной формы
 * @return array $errors - массив с ошибками
 */
function validateLotForm(array $lot): array
{
    $errors = [];
    $requiredFields = $lot;
    foreach ($requiredFields as $key => $value) {
        if (isFieldEmpty($value)) {
            $errors[$key] = isFieldEmpty($value);
            unset($requiredFields[$key]);
        }
    }
    $rules = [
        'lot-rate' => function ($lot) {
            return validatePrice($lot['lot-rate']);
        },
        'lot-date' => function ($lot) {
            return validateData($lot['lot-date']);
        },
        'lot-step' => function ($lot) {
            return validateStep($lot['lot-step']);
        }
    ];
    foreach ($requiredFields as $field => $value) {
        if (isset($rules[$field])) {
            $rule = $rules[$field];
            $errors[$field] = $rule($lot);
        }
    }
    $errors = array_filter($errors);

    return $errors;
}

/**
 * Функция проводит необходимые проверки для валидации формы регистрации юзера
 * @param array $errors массив с ошибками
 * @param array $user массив с данными из отправленной формы
 * @return array $errors - массив с ошибками
 */
function validateUser(array $errors, array $user): array
{
    $errors = $errors;
    if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Пожалуйста, введите корректный адрес почты';
        $errors['password'] = 'Пароль неверный';
    }
    foreach ($user as $key => $value) {
        if (isFieldEmpty($value)) {
            $errors[$key] = isFieldEmpty($value);
        }
    }
    $errors = array_filter($errors);

    return $errors;
}

/**
 * Вносит данные юзера в базу данных
 * @connection ресурс соединения
 * @param array $user массив с данными юзера
 */
function insertUserInDb($connection, array $user): bool
{
    $request = "INSERT INTO users (dt_reg, user_id, email, `name`, password, avat_path, `contact`)
    VALUES (NOW(), '" . $user['id'] . "', '" . $user['email'] . "', '" . $user['name'] . "', '" . $user['password'] . "', NULL, '" . $user['message'] . "')";
    $result = mysqli_query($connection, $request);

    return $result;
}

/**
 * Функция проверяет, есть ли в БД пользователь с указанным имейлом
 * @param $connection ресурс соединения
 * @param $email имейл пользователя
 * @return bool $answer нашелся или нет пользователь с таким адресом
 */
function isEmailExist($connection, $email): bool
{
    $request = "SELECT * FROM users WHERE email = '" . $email . "'";
    $result = readFromDatabase($request, $connection);
    if ($result) {
        $answer = true;
    } else {
        $answer = false;
    }

    return $answer;
}


/**
 * Функция возвращает из БД информацию о пользователе
 * @param $connection ресурс соединения
 * @param string $email имейл пользователя
 * @return array $result массив с информацией о пользователе
 */
function getUserInfo($connection, string $email): array
{
    $request = "SELECT * FROM users WHERE email = '" . $email. "'";
    $result = readFromDatabase($request, $connection);
    $result = $result[0];

    return $result;
}

/**
 * Функция возвращает из БД массив с карточками, в которых описание или название подходит поисковому запросу
 * @param $connection ресурс соединения
 * @param string $word слово, по которому производится поиск
 * @return array $cards массив с карточками лотов
 * @param int $limit по сколько карточек результатов запрашивать из БД
 * @param int $offset нужно ли смещение в выборке результатов
 */
function getSearchResults($connection, string $word , int $limit = 9, int $offset = 0): array
{

    $request = "SELECT title, st_price, image_path, dt_end, c.name AS category_name
    FROM lots AS l
    LEFT JOIN categories AS c
    ON c.id = l.cat_id WHERE win_id IS NULL AND dt_end > NOW()
    AND MATCH (title, descr) AGAINST ('" . $word . "') ORDER BY l.dt_create DESC LIMIT " . $limit . " OFFSET " . $offset;
    $cards = readFromDatabase($request, $connection);

    return $cards;
}



