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
    $request = "SELECT l.id, title AS name, st_price AS price, image_path AS url, c.name AS category, dt_end AS `time`
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
    $request = "INSERT INTO lots (dt_create, cat_id, title, descr, image_path, st_price, dt_end, step)
    VALUES (NOW(), '" . $lot['category'] . "', '" . $lot['lot-name'] . "', '" . $lot['message'] . "', '" . $lot['path'] . "', '" .  $lot['lot-rate'] . "', '" . $lot['lot-date'] . "', '" . $lot['lot-step'] . "')";
    $result = mysqli_query($connection, $request);

    return $result;
}

/**
 * Подставляет в форму значения, которые уже были заполнены юзером
 * @param $name имя поля в форме
 * @return возвращает заполненные значения либо пустые строки, если поля не были заполнены
 */
function getPostVal($name)
{
    return $_POST['$name'] ?? "";
}

