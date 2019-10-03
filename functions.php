<?php

/**
 * Принимает цену в виде дробного или целого числа, форматирует и возвращает в формате "15 000 ₽".
 *
 * @param float $price
 * @return string $resultPrice
 */
function priceFormat(float $price): string
{
    $resultPrice = ceil($price);
    $resultPrice = number_format($resultPrice, 0, '.', ' ');
    $resultPrice .= ' ₽';

    return $resultPrice;
}

/**
 * Принимает дату в будущем в формате "ГГГГ-ММ-ДД" и возвращает время до этой даты массивом вида ['ЧЧ','ММ']
 *
 * @param string $endDate
 * @return array
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
 * Обрабатывает ошибки подключения к БД, принимая вид ошибки 'connect' (ошибка подключения) или 'request' (ошибка запроса)
 * и возвращает строку с описанием ошибки
 *
 * @param string $mistake
 * @param resource $con
 * @return string $error
 */
function errorFilter(string $mistake, $con = null): string
{
    $error = '';
    if ($mistake === 'connect') {
        $error = mysqli_connect_error();
    } elseif ($mistake === 'request') {
        $error = mysqli_error($con);
    }
    return $error;
}

/**
 * Обрабатывает запросы на чтение из БД, принимая текст запроса и ресурс соединения,
 * и возвращая двумерный ассоциативный массив с результатами запроса к БД
 *
 * @param string $sql
 * @param resource $con
 * @return array $data
 */
function readFromDatabase(string $sql, $con): array
{
    $data = mysqli_query($con, $sql);
    $data = mysqli_fetch_all($data, MYSQLI_ASSOC);

    return $data;
}

/**
 * Принимает ресурс соединения и возвращает список категорий в виде двумерного массива
 *
 * @param resource $connection
 * @return array $categories
 */
function getCategories($connection): array
{
    $request = "SELECT * FROM categories";
    $categories = readFromDatabase($request, $connection);

    return $categories;
}

/**
 * Принимает ресурс соединения и возвращает список лотов в виде двумерного массива
 *
 * @param resource $connection
 * @param bool $isLimit нужны ли ограничения в показе результатов
 * @param int $limit по сколько карточек результатов запрашивать из БД
 * @param int $offset нужно ли смещение в выборке результатов
 * @return array
 */
function getCards($connection, $isLimit = false, $limit = 9, $offset = 0): array
{
    $request = "SELECT l.id, title, st_price, image_path, dt_end, c.name AS category_name 
    FROM lots AS l
    LEFT JOIN categories AS c
    ON c.id = l.cat_id
    WHERE win_id IS NULL AND dt_end > NOW()
    ORDER BY l.dt_create DESC";
    if ($isLimit) {
        $request .= " LIMIT " . $limit . " OFFSET " . $offset;
    }
    $cards = readFromDatabase($request, $connection);

    return $cards;
}


/**
 * Возвращает ассоциативный массив с данными запрашиваемого лота по его id
 *
 * @param $id
 * @param resource $connection
 * @return array $card
 */
function getCard($connection, $id): array
{
    $request = "SELECT l.id, title AS `name`, image_path AS url, c.name AS category, descr AS description, dt_end AS `time`, step, st_price, user_id
    FROM lots AS l
    LEFT JOIN categories AS c ON c.id = l.cat_id
    WHERE l.id = " . (int)$id;
    $card = readFromDatabase($request, $connection);


    return $card;
}

/**
 * Принимает id необходимого лота и возвращает максимальную ставку по нему, если она есть
 *
 * @param $id
 * @param resource $connection
 * @return int $maxBid
 */
function getMaxBid($connection, $id):?int
{
    $request = "SELECT MAX(price) as max_price FROM bids WHERE lot_id = " . (int)$id;
    $bidArray = mysqli_query($connection, $request);
    $maxBid = mysqli_fetch_assoc($bidArray);
    $maxBid = $maxBid['max_price'];

    return $maxBid;
}

/**
 * Принимает массив с данными лота и добавляет новый лот в БД
 *
 * @param resource $connection
 * @param array $lot
 * @return bool|mysqli_result $result
 */
function insertLotInDb($connection, array $lot)
{
    $category = mysqli_real_escape_string($connection, $lot['category']);
    $title = mysqli_real_escape_string($connection, $lot['lot-name']);
    $message = mysqli_real_escape_string($connection, $lot['message']);
    $date = mysqli_real_escape_string($connection, $lot['lot-date']);
    $request = "INSERT INTO lots (user_id, dt_create, cat_id, title, descr, image_path, st_price, dt_end, step)
    VALUES (" . (int)$lot['user_id'] . ", NOW(), '" . $category . "', '" . $title . "', '" . $message . "', '" . $lot['path'] . "', " . (int)$lot['lot-rate'] . ", '" . $date . "', " . (int)$lot['lot-step'] . ")";
    $result = mysqli_query($connection, $request);

    return $result;
}

/**
 * Принимает название поля в форме и подставляет в нее значения, которые уже были заполнены юзером либо пустую строку
 *
 * @param string $name
 * @return string
 */
function getPostVal(string $name): string
{
    return $_POST[$name] ?? "";
}

/**
 * Принимает название поля, проверяет, заполнено ли оно, и возвращает текст ошибки или пустую строку
 *
 * @param $field
 * @return string $result
 */
function isFieldEmpty($field): string
{
    if (empty($field) or $field === 'Выберите категорию') {
        $result = 'Поле необходимо заполнить';
    } else {
        $result = '';
    }
    return $result;
}

/**
 * Принимает цену, проверяет, правильно ли она указана,
 * и возвращает текст ошибки или пустую строку
 *
 * @param $price
 * @return string $result
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
 * Принимает путь к файлу, проверяет, является ли тип файла подходящим для формы лота
 * и возвращает текст ошибки или пустую строку
 *
 * @param string $path
 * @return string $result
 */
function validateImg($path): string
{
    if (!empty($path)) {
        if (mime_content_type($path) != 'image/png' && mime_content_type($path) != 'image/jpeg') {
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
 * Принимает дату, проверяет ее формат и что она не прошла, и возвращает текст ошибки или пустую строку
 *
 * @param string $date
 * @return string $result
 */
function validateData($date): string
{
    if (!is_date_valid($date)) {
        $result = 'Неправильный формат даты. Введите в формате \'ГГГГ-ММ-ДД\'';
    } elseif (strtotime($date) < strtotime('tomorrow')) {
        $result = 'Дата окончания торгов должна быть больше текущей хотя бы на один день';
    } else {
        $result = '';
    }
    return $result;
}

/**
 * Принимает шаг ставки, проверяет, чтобы он был больше нуля и целым числом,
 * и возвращает текст ошибки или пустую строку
 *
 * @param $step
 * @return string $result
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
 * Принимает массив с ошибками и название тега, по которому их искать.
 * Возвращает строку с классом ошибки или пустую.
 *
 * @param array $errors
 * @param string $name
 * @return string
 */
function errorClass(array $errors, string $name)
{
    if (isset($errors[$name])) {
        return 'form__item--invalid';
    }

    return '';

}

/**
 * Функция принимает массив с данными из отправленной формы
 * и проводит необходимые проверки для валидации формы добавления лота.
 * Возвращает массив с ошибками или пустой.
 *
 * @param array $lot
 * @return array $errors
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
 * Функция принимает массив с ошибками и массив с данными из отправленной формы.
 * Проводит необходимые проверки для валидации формы регистрации юзера.
 * Возвращает массив с ошибками или пустой
 *
 * @param array $errors
 * @param array $user
 * @return array $errors
 */
function validateUser(array $errors, array $user): array
{
    if (isset($user['email']) && !filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Пожалуйста, введите корректный адрес почты';
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
 * Принимает массив с данными юзера и вносит их в базу данных
 *
 * @param resource $connection
 * @param array $user
 * @return bool|mysqli_result $result
 */
function insertUserInDb($connection, array $user): bool
{
    $email = mysqli_real_escape_string($connection, $user['email']);
    $name = mysqli_real_escape_string($connection, $user['name']);
    $password = mysqli_real_escape_string($connection, $user['password']);
    $message = mysqli_real_escape_string($connection, $user['message']);
    $request = "INSERT INTO users (dt_reg, email, `name`, password, `contact`)
    VALUES (NOW(), '" . $email . "', '" . $name . "', '" . $password . "', '" . $message . "')";
    $result = mysqli_query($connection, $request);

    return $result;
}

/**
 * Функция по имейлу проверяет, есть ли в БД пользователь с указанным имейлом
 *
 * @param resource $connection
 * @param string $email
 * @return bool $answer
 */
function isEmailExist($connection, $email): bool
{
    $email = mysqli_real_escape_string($connection, $email);
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
 * Функция по имейлу возвращает из БД массив с информацией о пользователе
 *
 * @param resource $connection
 * @param string $email
 * @return array $result
 */
function getUserInfo($connection, string $email): array
{
    $email = mysqli_real_escape_string($connection, $email);
    $request = "SELECT * FROM users WHERE email = '" . $email . "'";
    $result = readFromDatabase($request, $connection);
    $result = $result[0];

    return $result;
}

/**
 * Функция возвращает из БД массив с карточками лотов, в которых описание или название подходит поисковому запросу
 *
 * @param resource $connection ресурс соединения
 * @param string $word слово, по которому производится поиск
 * @param string $fields поле или поля, по которым искать
 * @param bool $isLimit нужны ли ограничения в показе результатов
 * @param int $limit по сколько карточек результатов запрашивать из БД
 * @param int $offset нужно ли смещение в выборке результатов
 * @return array $cards
 */
function getSearchResults($connection, string $word, string $fields, bool $isLimit = false, int $limit = 9, int $offset = 0): array
{
    $fields = mysqli_real_escape_string($connection, $fields);
    $word = mysqli_real_escape_string($connection, $word);
    $request = "SELECT title, st_price, image_path, dt_end, c.name AS category_name, l.id
    FROM lots AS l
    LEFT JOIN categories AS c
    ON c.id = l.cat_id WHERE win_id IS NULL AND dt_end > NOW()
    AND MATCH (" . $fields . ") AGAINST ('" . $word . "') ORDER BY l.dt_create DESC";
    if ($isLimit) {
        $request .= " LIMIT " . $limit . " OFFSET " . $offset;
    }
    $cards = readFromDatabase($request, $connection);

    return $cards;
}

/**
 * Функция возвращает из БД массив с карточками лотов, в которых описание или название подходит поисковому запросу
 *
 * @param resource $connection ресурс соединения
 * @param string $id категории
 * @param bool $isLimit нужны ли ограничения в показе результатов
 * @param int $limit по сколько карточек результатов запрашивать из БД
 * @param int $offset нужно ли смещение в выборке результатов
 * @return array $cards
 */
function getSearchCategory($connection, string $id, bool $isLimit = false, int $limit = 9, int $offset = 0): array
{
    $request = "SELECT title, st_price, image_path, dt_end, c.name AS category_name, l.id
    FROM lots AS l
    LEFT JOIN categories AS c
    ON c.id = l.cat_id WHERE win_id IS NULL AND dt_end > NOW()
    AND c.id = " . (int)$id . " ORDER BY l.dt_create DESC";
    if ($isLimit) {
        $request .= " LIMIT " . $limit . " OFFSET " . $offset;
    }
    $cards = readFromDatabase($request, $connection);

    return $cards;
}

/**
 * Функция принимает массив с данными ставки и минимальную возможную ставку.
 * Затем проверяет заполнение формы ставки и возвращает массив с ошибками или пустой
 *
 * @param array $bid
 * @param $minBid
 * @return array $errors
 */
function validateBid($bid, $minBid): array
{
    $errors = [];
    $value = $bid['cost'];
    if (isFieldEmpty($value)) {
        $errors['cost'] = isFieldEmpty($value);
        return $errors;
    }
    $cost = ($value == (int) $value) ? (int) $value : (float) $value;
    if (!is_int($cost) or $cost < 0 or !is_numeric($bid['cost'])) {
        $errors['cost'] = 'Ставка должна быть целым положительным числом';
        return $errors;
    }
    if ($cost < $minBid) {
        $errors['cost'] = 'Ставка должна быть не меньше минимальной';
    }
    $errors = array_filter($errors);

    return $errors;
}

/**
 * Функция принимает ресурс соединения, массив ставки и вносит данные о сделанной ставке
 *
 * @param resource $connection
 * @param array $bid
 * @return bool|mysqli_result $result
 */
function insertBidInDb($connection, array $bid)
{
    $request = "INSERT INTO bids (dt_create, user_id, lot_id, price)
    VALUES (NOW(), " . (int)$bid['user_id'] . ", " . (int)$bid['lot_id'] . ", " . (int)$bid['cost'] . ")";
    $result = mysqli_query($connection, $request);

    return $result;
}


/**
 * Функция возвращает двумерный массив со всеми ставками по id лота
 *
 * @param resource $connection
 * @param $id
 * @return array $bids
 */
function getBids($connection, $id): array
{
    $request = "SELECT u.name as user_name, lot_id, dt_create, price FROM bids as b 
    LEFT JOIN users as u ON b.user_id = u.id WHERE lot_id = " . (int)$id . " ORDER BY dt_create DESC";
    $bids = readFromDatabase($request, $connection);

    return $bids;
}

/**
 * Функция принимает время, когда была сделана ставка, для преобразования и возвращает его в человеческом виде
 *
 * @param $time
 * @return string $result
 */
function bidTime($time): string
{
    date_default_timezone_set('Europe/Moscow');

    $dateNow = date_create('now');
    $datePast = date_create($time);
    $timeInterval = date_diff($datePast, $dateNow);
    $hours = date_interval_format($timeInterval, '%h');
    $mins = date_interval_format($timeInterval, '%i');
    $days = date_interval_format($timeInterval, '%d');

    if ($days >= 1 && $days < 2) {
        $result = 'Вчера, в ' . date('H:i', strtotime($time));
    } elseif ($days >= 2) {
        $result = date('d.m.Y', strtotime($time)) . ' в ' . date('H:i', strtotime($time));
    } else {
        if ($hours < 1) {
            $result = $mins . ' ' . get_noun_plural_form($mins, 'минуту', 'минуты', 'минут') . ' назад';
        } elseif ($hours >= 1 && $hours < 2) {
            $result = 'Час назад';
        } else {
            $result = $hours . ' ' . get_noun_plural_form($hours, 'час', 'часа', 'часов') . ' назад';
        }
    }

    return $result;
}

/**
 * Функция по id юзера возвращает массив с данными по всем его ставкам
 *
 * @param resource $connection
 * @param $id
 * @return array $bids
 */
function getUserBids($connection, $id): array
{
    $bidRequest = "SELECT l.image_path as image, l.title as lot_title, c.name as category, l.dt_end, b.price, b.lot_id, b.dt_create, l.win_id as winner, l.user_id as lot_owner
    FROM bids b LEFT JOIN lots l ON b.lot_id = l.id 
    JOIN categories c ON l.cat_id = c.id
    WHERE b.user_id = " . (int)$id . " ORDER BY b.dt_create DESC";
    $bids = readFromDatabase($bidRequest, $connection);
    foreach ($bids as $key => $bid) {
        if ($bids[$key]['winner'] !== NULL && $bids[$key]['winner'] == $id) {
            $contactRequest = "SELECT contact FROM users WHERE id = " . $bids[$key]['lot_owner'];
            $contact = readFromDatabase($contactRequest, $connection);
            $bids[$key]['contact'] = $contact[0]['contact'];
            if ($bids[$key]['price'] == getMaxBid($connection, $bids[$key]['lot_id'])) {
                $bids[$key]['isMax'] = true;
            }
        }
    }

    return $bids;
}

/**
 * Функция принимает массив времени окончания ставки, и возвращает строку с классом окончания времени или пустую
 *
 * @param array $time
 * @return string
 */
function timeClass(array $time): string
{
    if ($time['hours'] < 1 && $time['days'] == 0) {
        $timeClass = ' timer--finishing';
        return $timeClass;
    }
    return '';
}

/**
 * Функция принимает массив с данными ставки, id юзера и возвращает массив с классами для ставки
 *
 * @param array $bid
 * @param $user_id
 * @return array $class
 */
function bidClass(array $bid, $user_id): array
{
    $class = [];
    $time = timeCounter($bid['dt_end']);
    if ($bid['winner'] === $user_id && isset($bid['isMax'])) {
        $class['item'] = 'rates__item--win';
        $class['timer'] = 'timer--win';
        $class['text'] = 'Ставка выиграла';
    } elseif (strtotime($bid['dt_end']) < strtotime('now')) {
        $class['item'] = 'rates__item--end';
        $class['timer'] = 'timer--end';
        $class['text'] = 'Торги окончены';
    } else {
        $class['timer'] = timeClass($time);
    }

    return $class;
}

/**
 * Функция находит в БД все лоты без победителя, дата истечения которых меньше или равна текущей дате
 *
 * @param resource $connection
 * @return array $lots
 */
function getLotsWithoutWinner($connection): array
{
    $request = "SELECT id FROM lots WHERE win_id IS NULL and dt_end <= CURRENT_DATE()";
    $lots = readFromDatabase($request, $connection);

    return $lots;
}

/**
 * Функция возвращает id владельца последней ставки по id лота
 *
 * @param resource $connection
 * @param $id
 * @return int $winner
 */
function getLastBid($connection, $id)
{
    $request = "SELECT user_id FROM bids WHERE lot_id = " . (int)$id . " ORDER BY dt_create DESC LIMIT 1";
    $winner = readFromDatabase($request, $connection);
    if (!empty($winner[0])) {
        $winner = $winner[0]['user_id'];
    } else {
        $winner = null;
    }

    return $winner;
}

/**
 * Функция записывает в таблицу лотов id победителя
 *
 * @param resource $connection
 * @param $lot
 * @param $winner
 * @return bool|mysqli_result $result
 */
function insertWinnerInDB($connection, $lot, $winner)
{
    $request = "UPDATE lots SET win_id = " . (int)$winner . " WHERE id = " . (int)$lot;
    $result = mysqli_query($connection, $request);

    return $result;
}

/**
 * Функция по id юзера возвращает массив с данными о победителе торгов, содержащий:
 * `name` - имя победителя, email - почту победителя, lot_id - id лота, который он выиграл, title - название выигранного лота
 *
 * @param resource $connection
 * @param $winner
 * @return array $winData
 */
function getWinData($connection, $winner): array
{
    $request = "SELECT email, u.name, l.id as lot_id, l.title FROM users u
    JOIN lots l ON l.win_id = u.id
    WHERE u.id = " . (int)$winner;
    $winData = readFromDatabase($request, $connection);

    return $winData;
}

/**
 * Функция принимает ассоциативный массив, где name - имя победителя,
 * email - почта, $lot_id - id выигранного лота, title - название выигранного лота,
 * и отправляет письмо победителю
 *
 * @param array $winData
 * @return void
 */
function sendWinEmail(array $winData): void
{
    $winData = $winData[0];
    $userName = $winData['name'];
    $email = $winData['email'];
    $lot = $winData['lot_id'];
    $title = $winData['title'];

    $transport = new Swift_SmtpTransport('smtp.mailtrap.io', 25);
    $transport->setUsername('832f83bcd7e474');
    $transport->setPassword('91ff248f22ca37');

    $message = new Swift_Message("Ваша ставка победила");
    $message->setTo([$email => $userName]);
    $emailBody = include_template('email.php', [
        'userName' => $userName,
        'lot_id' => $lot,
        'title' => $title
    ]);
    $message->addPart($emailBody, 'text/html');
    $message->setFrom(['keks@phpdemo.ru' => 'Yeticave']);

    $mailer = new Swift_Mailer($transport);
    $mailer->send($message);
    return;
}

/**
 * Функция принимает массив с категориями и возвращает код страницы ошибки 404
 *
 * @param array $categories
 * @return $layoutContent
 */
function error404($categories)
{
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

    return $layoutContent;
}