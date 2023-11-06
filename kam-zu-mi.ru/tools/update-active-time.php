<?php

// Регламентный скрипт.
// Проходит по блюдам, отключает\включает их по времени (свойство ACTIVE_TIME)
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
set_time_limit(0);
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

\Bitrix\Main\Loader::IncludeModule('iblock');

use \Bitrix\Iblock\ElementTable;

const IBLOCK_ID = 25;

// Проверяет, попадает ли текущее время в заданный интервал
// Формат интервала, пример, 09:00-16:00
// Если формат не тот, вернёт null
function isInTimeInterval($intervalStr)
{
    $result = null;

    $isMathed = preg_match('/(\d{2}):(\d{2})-(\d{2}):(\d{2})/', $intervalStr, $matches);
    if (!$isMathed) return $result;

    $timeNow = new DateTime('NOW', new DateTimeZone('Europe/Kaliningrad'));

    $timeFrom = new DateTime('NOW', new DateTimeZone('Europe/Kaliningrad'));
    $timeFrom->setTime($matches[1], $matches[2]);

    $timeTo = new DateTime('NOW', new DateTimeZone('Europe/Kaliningrad'));
    $timeTo->setTime($matches[3], $matches[4]);

    if ($timeFrom <= $timeNow && $timeNow <= $timeTo) {
        $result = true;
    } else {
        $result = false;
    }

    return $result;
}

$products = ElementTable::getList(array(
    'filter' => array(
        'IBLOCK_ID' => IBLOCK_ID,
    ),
    'select' =>  array('ID', 'ACTIVE'),
));

while ($product = $products->fetchObject()) {
    $db_props = CIBlockElement::GetProperty(IBLOCK_ID, $product["ID"], array("sort" => "asc"), array("CODE" => "ACTIVE_TIME"));
    if ($ar_props = $db_props->Fetch()) {
        if ($ar_props['VALUE']) {
            $explodedActiveTime = explode(';', $ar_props['VALUE']);
            if (count($explodedActiveTime) == 7) {
                $today = new DateTime();
                $weekDay = $today->format('N');
                $timeStr = $explodedActiveTime[$weekDay - 1];

                $res = isInTimeInterval($timeStr, true);

                if ($res !== null && $product["ACTIVE"] !== $res) {
                    $obEl = new CIBlockElement();
                    $boolResult = $obEl->Update($product["ID"], array("ACTIVE" => $res ? "Y" : "N"));
                } elseif ($res === null && $product["ACTIVE"] != 1) {
                    $obEl = new CIBlockElement();
                    $boolResult = $obEl->Update($product["ID"], array("ACTIVE" => "Y"));
                }
            }
        }
    }
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
