<?php

$postData = file_get_contents('php://input');
$data = json_decode($postData, true);
$date_create = $data['date_create'];
$sum = $data['sum'];
$age = $data['age'];
$reple = $data['reple'];
$add = $data['add'];
/*
 * 4.5 Формула с капитализаций процентов по вкладу:
    4.5.1 summn = summn-1 + (summn-1 + summadd)$days_in_mount(PERCENT / $days_in_year)
    4.5.2 где summn – сумма на счете на месяц n (руб)
    4.5.3 summn-1 – сумма на счете на конец прошлого месяца
    4.5.4 summadd – сумма ежемесячного пополнения
    4.5.5 $days_in_mount – количество дней в данном месяце, на которые приходился вклад
    4.5.6 PERCENT – процентная ставка банка - 10%
    4.5.7 $days_in_year – количество дней в году.
*/

define("PERCENT", "0.1");

$date[] = date_parse_from_format("Y.m.d", $date_create);
$year = $date[0]["year"];
$month = $date[0]["month"];
$days_in_mount = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$days_in_year = (date('L', strtotime($date_create))? 366:365);
$count = (int)$age * 12 -1;
$body = $sum + $sum * $days_in_mount * (PERCENT / $days_in_year);


if ((bool)$reple == false) {$add = 0;} // не учитываем ежемесячный взнос
for($i=0; $i < $count; $i++)
{
    $days_in_mount = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $days_in_year = (date('L', strtotime($date_create))? 366:365);
    $body = plowback($body, $add, $days_in_mount, $days_in_year);
}


function plowback($body,  $add, $days_in_mount, $days_in_year)
{
    return $body + ($body + $add) * $days_in_mount * (PERCENT / $days_in_year);
}

echo json_encode(intval($body));