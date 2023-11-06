<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

//echo $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
set_time_limit(0);

CModule::IncludeModule('reklamafia.iiko');
CModule::IncludeModule('sale');
$likoApi = new \Reklamafia\Iiko\IikoApi();

$arrComposeStatuses = array(
    'Новая' => 'KA',//новая
    'Ждет отправки' => 'KB',//Ждет отправки
    'В пути' => 'KC',//В пути
    'Закрыта' => 'KD',//Закрыта
    'Отменена' => 'KE',//Отменена
    'Доставлена' => 'KF',//Доставлена
    'Не подтверждена' => 'KG',//Не подтверждена
    'Готовится' => 'KH',//Готовится
    'Готово' => 'KI',//Готово
);


$nTopCount = false;
$nTopCount = array('nTopCount' => 20);

$arFilter = Array(
    "!STATUS_ID" => array("F", "KD", "KE", "IE", "IF", "IG", "IJ", "IL", "IM"),
    "!PROPERTY_VAL_BY_CODE_IIKO_ID" => false,
);

$rsSales = CSaleOrder::GetList(array("ID" => "DESC"), $arFilter, false, $nTopCount);

$i = 0;
while ($arSales = $rsSales->Fetch()) {

    $arOredProps = array();
    $dbOrderProps = CSaleOrderPropsValue::GetOrderProps($arSales["ID"]);
    while ($arProp = $dbOrderProps->Fetch()) {
        $arOredProps[$arProp['CODE']] = $arProp;
    }

    if ($likoOrderId = $arOredProps['IIKO_ID']['VALUE']) {
        $order = new \Reklamafia\Iiko\IikoOrder($likoOrderId);
        $likoOrder = $likoApi->getOrder($order);
        $likoOrderStatus = $likoOrder->status;

        $orderNewStatus = $arrComposeStatuses[$likoOrderStatus];

        if ($orderNewStatus) {
            $arFieldsNew = array(
                "STATUS_ID" => $orderNewStatus,
            );
            CSaleOrder::Update($arSales["ID"], $arFieldsNew);
            //echo "<pre>"; print_r($arSales["ID"]); echo "</pre>";
            //AddMessage2Log('$arFields = '.print_r($arSales["ID"], true),'');
        }

    }
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
?>