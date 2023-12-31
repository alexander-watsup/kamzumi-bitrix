<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;

if (!Loader::includeModule('intec.startshop'))
    return;

/** Prices List */
$arPricesTypes = [];
$arPriceIDs = [];

$dbPricesTypes = CStartShopPrice::GetList(['SORT' => 'ASC']);

while ($arPriceType = $dbPricesTypes->Fetch()) {
    $arPricesTypes[$arPriceType['CODE']] = '['.$arPriceType['CODE'].'] '.$arPriceType['LANG'][LANGUAGE_ID]['NAME'];
    $arPriceIDs[$arPriceType['ID']] = '['.$arPriceType['ID'].'] '.$arPriceType['LANG'][LANGUAGE_ID]['NAME'];
}
unset($dbPricesTypes, $arPriceType);

/** Currencies */
$arCurrencies = [];
$dbCurrencies = CStartShopCurrency::GetList();

while ($arCurrency = $dbCurrencies->Fetch())
    $arCurrencies[$arCurrency['CODE']] = '['.$arCurrency['CODE'].'] '.$arCurrency['LANG'][LANGUAGE_ID]['NAME'];
unset($dbCurrencies, $arCurrency);

$arTemplateParameters += [
    'PRICE_CODE' => [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('C_NEWS_BRANDS_1_PRICE_CODE'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $arPricesTypes
    ],
    'CONVERT_CURRENCY' => [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('C_NEWS_BRANDS_1_USE_COMMON_CURRENCY'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ]
];
if ($arCurrentValues['CONVERT_CURRENCY'] == 'Y') {
    $arTemplateParameters['CURRENCY_ID'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('C_NEWS_BRANDS_1_CURRENCY'),
        'TYPE' => 'LIST',
        'VALUES' => $arCurrencies
    ];
}

