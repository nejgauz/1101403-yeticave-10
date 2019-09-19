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
    $card = $card[0];

    return $card;
}

/**
 * Возвращает максимальную ставку, если она есть
 * @param $id - id необходимого лота
 * @param $connection ресурс соединения
 * @return $maxBid возвращает максимальную ставку
 */
function getMaxBid($connection, $id)
{
    $request = "SELECT MAX(price) as max_price FROM bids WHERE lot_id = " . $id;
    $bidArray = mysqli_query($connection, $request);
    $maxBid = mysqli_fetch_assoc($bidArray);
    $maxBid = $maxBid['max_price'];

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
    if (empty($field) or $field === 'Выберите категорию') {
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
    $request = "INSERT INTO users (dt_reg, email, `name`, password, avat_path, `contact`)
    VALUES (NOW(), '" . $user['email'] . "', '" . $user['name'] . "', '" . $user['password'] . "', NULL, '" . $user['message'] . "')";
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
 * @param string $fields поле или поля, по которым искать
 * @param int $limit по сколько карточек результатов запрашивать из БД
 * @param int $offset нужно ли смещение в выборке результатов
 * @return array $cards массив с карточками лотов
 */
function getSearchResults($connection, string $word, string $fields, int $limit = 9, int $offset = 0): array
{

    $request = "SELECT title, st_price, image_path, dt_end, c.name AS category_name, l.id
    FROM lots AS l
    LEFT JOIN categories AS c
    ON c.id = l.cat_id WHERE win_id IS NULL AND dt_end > NOW()
    AND MATCH (" . $fields . ") AGAINST ('" . $word . "') ORDER BY l.dt_create DESC LIMIT " . $limit . " OFFSET " . $offset;
    $cards = readFromDatabase($request, $connection);

    return $cards;
}

/**
 * Функция проверяет заполнение формы ставки и возвращает массив с ошибками
 * @param array $bid массив ставки
 * @param $minBid минимальная возможная ставка
 * @return array $errors массив с ошибками
 */
function validateBid($bid, $minBid): array
{
    $errors['cost'] = isFieldEmpty($bid);
    if (!intval($bid['cost']) or $bid['cost'] < 0) {
        $errors['cost'] = 'Ставка должна быть целым положительным числом';
        return $errors;
    }
    if ($bid['cost'] < $minBid) {
        $errors['cost'] = 'Ставка должна быть не меньше минимальной';
    }
    $errors = array_filter($errors);

    return $errors;
}

/**
 * Функция вносит данные о сделанной ставке
 * @param $connection ресурс соединения
 * @param $bid ставка
 * @return bool $result получилось или нет внести в базу
 */
function insertBidInDb($connection, $bid)
{
    $request = "INSERT INTO bids (dt_create, user_id, lot_id, price)
    VALUES (NOW(), " . $bid['user_id'] . ", " . $bid['lot_id'] . ", " . $bid['cost'] . ")";
    $result = mysqli_query($connection, $request);

    return $result;
}

/**
 * Функция возвращает максимальную цену
 * @param $curPrice текущая цена
 * @param $maxBid максимальная ставка
 * @return $maxPrice максимальная цена
 */
function getMaxPrice($curPrice, $maxBid)
{
    if ($curPrice > $maxBid) {
        $maxPrice = $curPrice;
    } else {
        $maxPrice = $maxBid;
    }

    return $maxPrice;
}

/**
 * Функция возвращает массив со всеми ставками по данному лоту
 * @param $connection ресурс соединения
 * @param $id id лота
 * @return array $bids двумерный массив со всеми ставками
 */
function getBids($connection, $id): array
{
    $request = "SELECT u.name as user_name, lot_id, dt_create, price FROM bids as b 
    LEFT JOIN users as u ON b.user_id = u.id WHERE lot_id = " . $id . " ORDER BY dt_create DESC";
    $bids = readFromDatabase($request, $connection);

    return $bids;
}

/**
 * Функция, возвращающая время, когда была сделана ставка в человеческом виде
 * @param $time время для преобразования
 * @return $result строка с отформатированным временем
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

    if ($days >=1 && $days < 2) {
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
 * Функция, по id юзера возвращает массив с данными по всем его ставкам
 * @param $connection ресурс соединения
 * @param $id id пользователя
 * @return array $bids двумерный массив со ставками
 */
function getUserBids($connection, $id): array
{
    $request = "SELECT l.image_path as image, b.lot_id, l.title as lot_title, c.name as category, dt_end, b.price, b.dt_create, win_id as winner, l.user_id as lot_owner
    FROM bids b LEFT JOIN lots l ON b.lot_id = l.id 
    JOIN categories c ON l.cat_id = c.id
    JOIN users u ON l.user_id = u.id
    WHERE b.user_id = " . $id . " ORDER BY dt_end DESC";
    $bids = readFromDatabase($request, $connection);


    return $bids;
}

/**
 * Функция проверяет, нужен ли класс окончания времени
 * @param array $time массив времени окончания ставки
 * @return string строку с классом или пустую строку
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
 * Функция возвращает класс для ставки
 * @param array $bid массив с данными ставки
 * @para, $user_id
 * @return array $class массив с классами
 */
function bidClass(array $bid, $user_id): array
{
    $class = [];
    $time = timeCounter($bid['dt_end']);
    if ($bid['winner'] === $user_id) {
        $class['item'] = 'rates__item--win';
        $class['timer'] = 'timer--win';
        $class['text'] = 'Ставка выиграла';
    } elseif (strtotime($bid['dt_end']) < strtotime('today') ) {
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
 * @param $connection ресурс соединения
 * @return array $lots id лотов без победителя
 */
function getLotsWithoutWinner($connection): array
{
    $request = "SELECT id FROM lots WHERE win_id IS NULL and dt_end <= CURRENT_DATE()";
    $lots = readFromDatabase($request, $connection);

    return $lots;
}

/**
 * Функция возвращает id владельца последней ставки по id лота
 * @param $connection ресурс соединения
 * @param $id id лота
 * @return int $winner id юзера, который сделал последнюю ставку по данному лоту
 */
function getLastBid($connection, $id)
{
    $request = "SELECT user_id FROM bids WHERE lot_id = " . $id . " ORDER BY dt_create DESC LIMIT 1";
    $winner = readFromDatabase($request, $connection);
    if (!empty($winner[0])) {
        $winner = $winner[0]['user_id'];
    } else {
        $winner = null;
    }

    return $winner;
}

/**
 * Функция записывает в БД id победителя
 * @param $connection ресурс соединения
 * @param $lot id лота
 * @param $winner id победителя
 * @return $result получилось или нет обновить информацию в БД
 */
function insertWinnerInDB($connection, $lot, $winner)
{
    $request = "UPDATE lots SET win_id = " . $winner . " WHERE id = " . $lot;
    $result = mysqli_query($connection, $request);

    return $result;
}

/**
 * Функция возвращает массив с данными о победителе торгов
 * @param $connection ресурс соединения
 * @param $winner id победителя
 * @return $winData ассоциативный массив, содеражащий: `name` - имя победителя, email - почту победителя, lot_id - id лота, который он выиграл,
 * title - название выигранного лота
 */
function getWinData($connection, $winner): array
{
    $request = "SELECT email, u.name, l.id as lot_id, l.title FROM users u
    JOIN lots l ON l.win_id = u.id
    WHERE u.id = " . $winner;
    $winData = readFromDatabase($request, $connection);

    return $winData;
}

/**
 * Функция отправляет письмо победителю
 * @param $winData ассоциативный массив, где name - имя победителя, email - почта, $lot_id - id выигранного лота, title - название выигранного лота
 * @param $email - почта победителя
 * @param $lot_id - id выигранного лота
 * @param $title - название выигранного лота
 * @return true или false получилось или нет отправить письмо
 */
function sendWinEmail(array $winData): bool
{
    $userName = $winData[0]['name'];
    $email = $winData[0]['email'];
    $lot = $winData[0]['lot_id'];
    $title = $winData[0]['title'];

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
    if ($mailer->send($message)) {
        return true;
    }
    return false;
}
