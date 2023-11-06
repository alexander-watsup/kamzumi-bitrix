<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$result['COLUMNS'] = [];
$result['COLUMNS'][] = [
	'label' => Loc::getMessage('RS_VSA_COLUMN_NAME'),
    'field' => 'NAME',
    'sortable' => true,
    'html' => false,
];
$result['COLUMNS'][] = [
	'label' => Loc::getMessage('RS_VSA_COLUMN_PRICE'),
	'field' => 'PRICE',
	'type' => 'number',
    'sortable' => true,
    'html' => false,
];
$result['COLUMNS'][] = [
	'label' => Loc::getMessage('RS_VSA_COLUMN_QUANTITY'),
	'field' => 'QUANTITY',
	'type' => 'number',
    'sortable' => true,
    'html' => false,
];
$result['COLUMNS'][] = [
	'label' => Loc::getMessage('RS_VSA_COLUMN_SUM'),
	'field' => 'SUM_PRICE',
	'type' => 'number',
    'sortable' => true,
    'html' => false,
];

foreach ($result['ROWS'] as &$row)
{
	$row['PRICE_FORMATTED'] = CCurrencyLang::CurrencyFormat($row['PRICE'], $row['CURRENCY']);
	$row['SUM_PRICE_FORMATTED'] = CCurrencyLang::CurrencyFormat($row['SUM_PRICE'], $row['CURRENCY']);
}
unset($row);

$result['SUMMARY']['PRICE_FORMATTED'] = CCurrencyLang::CurrencyFormat($result['SUMMARY']['PRICE'], $result['SUMMARY']['CURRENCY']);