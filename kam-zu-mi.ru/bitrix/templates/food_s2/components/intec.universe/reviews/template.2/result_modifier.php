<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

global $USER;

$arParams = ArrayHelper::merge([
    'POST_USE' => 'N',
    'POST_ALLOW' => 'registered',
    'DATE_SHOW' => 'N',
    'DATE_FORMAT' => 'd.m.Y',
    'CONSENT_URL' => null
], $arParams);

$arVisual = [
    'POST' => [
        'USE' => $arParams['POST_USE'] === 'Y',
        'ALLOW' => false
    ],
    'CAPTCHA' => [
        'USE' => $arParams['USE_CAPTCHA'] === 'Y'
    ],
    'DATE' => [
        'SHOW' => $arParams['DATE_SHOW'] === 'Y',
        'FORMAT' => $arParams['DATE_FORMAT']
    ],
    'CONSENT' => [
        'SHOW' => !defined('EDITOR') ? Properties::get('base-consent') : false,
        'URL' => StringHelper::replaceMacros($arParams['CONSENT_URL'], [
            'SITE_DIR' => SITE_DIR
        ])
    ]
];

if ($arVisual['POST']['USE']) {
    if ($arParams['POST_ALLOW'] === 'registered')
        $arVisual['POST']['ALLOW'] = $USER->IsAuthorized();
    else if ($arParams['POST_ALLOW'] === 'all')
        $arVisual['POST']['ALLOW'] = true;
}

foreach ($arResult['ELEMENTS'] as &$arItem) {
    $arItem['DATA'] = [
        'DATE' => null
    ];

    if (!empty($arItem['DATE_CREATE'])) {
        $arItem['DATA']['DATE'] = CIBlockFormatProperties::DateFormat(
            $arVisual['DATE']['FORMAT'],
            MakeTimeStamp(
                $arItem['DATE_CREATE'],
                CSite::GetDateFormat()
            )
        );
    }
}

unset($arItem);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);