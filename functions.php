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
 * Обрабатывает ошибки подключения к БД и показывает страницу с ее типом
 * @param string $mistake вид ошибки: 'connect' (ошибка подключения) или 'request' (ошибка запроса)
 * @param $con ресурс соединения
 */
function errorFilter(string $mistake, $con)
{
    if ($mistake === 'connect') {
        $error = mysqli_connect_error();
        $pageContent = include_template('error.php', $error);
        echo $pageContent;
    } else {
        $error = mysqli_error($con);
        $pageContent = include_template('error.php', $error);
        echo $pageContent;
    }
}

/**
 * Обрабатывает запросы на чтение из БД
 * @param string $sql текст запроса к БД
 * @param $con ресурс соединения
 * @return array двумерный ассоциативный массив с результатами запроса к БД
 */
function readFromDatabase(string $sql, $con): array
{
    if (!$con) {
        errorFilter('connect', NULL);
    } else {
        $data = mysqli_query($con, $sql);
        if (!$data) {
            errorFilter('request', $con);
        } else {
            $data = mysqli_fetch_all($data, MYSQLI_ASSOC);
            return $data;
        }
    }
}
