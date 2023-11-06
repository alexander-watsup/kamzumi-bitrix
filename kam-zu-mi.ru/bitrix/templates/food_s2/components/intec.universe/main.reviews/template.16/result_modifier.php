<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_LINK' => null,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PROPERTY_RATING' => null,
    'RATING_SHOW' => 'N',
    'RATING_MAX' => '5',
    'BUTTON_ALL_SHOW' => 'N',
    'ACTIVE_DATE_SHOW' => 'N',
    'ACTIVE_DATE_FORMAT' => 'd.m.Y',
    'BUTTON_ALL_SHOW' => 'N',
    'SLIDER_LOOP' => 'N',
    'SLIDER_AUTO_USE' => 'N',
    'SLIDER_AUTO_TIME' => 10000,
    'SLIDER_AUTO_HOVER' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arMacros = [
    'SITE_DIR' => SITE_DIR
];

$hGetProperty = function (&$arItem, $property) {
    $property = ArrayHelper::getValue($arItem, [
        'PROPERTIES',
        $property,
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        return $property;
    }

    return null;
};

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [];

    if (!empty($arParams['PROPERTY_RATING'])) {
        $arProperty = $hGetProperty($arItem, $arParams['PROPERTY_RATING']);

        if (!empty($arProperty))
            $arItem['DATA']['RATING'] = $arProperty;
    }

    if (strlen($arItem['ACTIVE_FROM'])>0)
        $arItem['DISPLAY_ACTIVE_FROM'] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
    elseif (strlen($arItem['DATE_CREATE'])>0)
        $arItem['DISPLAY_ACTIVE_FROM'] = CIBlockFormatProperties::DateFormat($arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["DATE_CREATE"], CSite::GetDateFormat()));
    else
        $arItem['DISPLAY_ACTIVE_FROM'] = '';
}
unset($arItem);

unset($hGetProperty);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y'
    ],
    'RATING' => [
        'SHOW' => $arParams['RATING_SHOW'] === 'Y' && !empty($arParams['PROPERTY_RATING']),
        'MAX' => intval($arParams['RATING_MAX'])
    ],
    'ACTIVE_DATE' => [
        'SHOW' => $arParams['ACTIVE_DATE_SHOW'] === 'Y'
    ],
    'SLIDER' => [
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTO_USE'] === 'Y',
            'TIME' => Type::toInteger($arParams['SLIDER_AUTO_TIME']),
            'HOVER' => $arParams['SLIDER_AUTO_HOVER'] === 'Y'
        ]
    ],
    'BUTTON_SHOW_ALL' => [
        'SHOW' => $arParams['BUTTON_ALL_SHOW'] === 'Y' && !empty($arParams['LIST_PAGE_URL']),
        'TEXT' => $arParams['BUTTON_ALL_TEXT'],
        'LINK' => StringHelper::replaceMacros(
            $arParams['LIST_PAGE_URL'],
            $arMacros
        )
    ]
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if ($arVisual['SLIDER']['AUTO']['TIME'] < 1)
    $arVisual['SLIDER']['AUTO']['TIME'] = 10000;

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'], $arVisual);

unset($arVisual);

if (!empty($arParams['LIST_PAGE_URL']))
    $arButtons['SHOW_ALL']['LINK'] = StringHelper::replaceMacros(
        $arParams['LIST_PAGE_URL'],
        $arMacros
    );

unset($arMacros);