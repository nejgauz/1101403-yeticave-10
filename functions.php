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

/*
 1) создать экземпляр даты н.в.
 2) создать экземпляр даты параметра
 3) создать экземпляр промежутка в нужном формате (+ нужна еще одна функция для форматирования)
 4) узнать, сколько часов в промежутке, функцией floor
 5) узнать сколько минут, вычтя из промежутка кол-во часов
 6) если осталось меньше часа, то надо выделить время красным цветом
 7) касательно отрицательного промежутка - надо подумать об этом в форме?
 */
/**
 * Принимает дату в будущем в формате "ГГГГ-ММ-ДД" и возвращает время до этой даты в виде массива ['ЧЧ','ММ']
 */
function timeCounter($endDate): array
{
    date_default_timezone_set('Europe/Moscow');

    $dateNow = date_create('now');
    $dateFuture = date_create($endDate);
    $timeRest = date_diff($dateFuture, $dateNow);
    $hoursRest = date_interval_format($timeRest, '%H');
    $minRest = date_interval_format($timeRest, '%M');

    return [$hoursRest, $minRest];

}
