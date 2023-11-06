<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
$basketItems = $basket->getOrderableItems();

$basketFormatted = [];
foreach ($basketItems as $basketItem) {
    $item = [
        'id' => $basketItem->getField("PRODUCT_XML_ID"),
        'name' => $basketItem->getField("NAME"),
        'quantity' => $basketItem->getQuantity(),
        'price' => $basketItem->getPrice(),
    ];
    $basketFormatted[] = $item;
}

$iiko = new \Reklamafia\Exchange\IIKO();
$deliveryCities = $iiko->getCities();
$pickupPoints = $iiko->getTerminals();

$arResult['RM'] = [
    'basketFormatted' => $basketFormatted,
    'deliveryCities' => $deliveryCities,
    'pickupPoints' => $pickupPoints
];

$this->IncludeComponentTemplate();
//echo "1";
//var_dump($basketItems->getListOfFormatText());
