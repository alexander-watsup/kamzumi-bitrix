<?
 require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Оформление заказа");

 $APPLICATION->IncludeComponent(
"reklamafia:basket",
".default",
Array(
),
false
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");