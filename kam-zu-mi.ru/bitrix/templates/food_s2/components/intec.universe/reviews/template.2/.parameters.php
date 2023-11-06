<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

$arTemplateParameters = [];

$arTemplateParameters['POST_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_2_POST_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['POST_USE'] === 'Y') {
    $arTemplateParameters['POST_ALLOW'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_2_POST_ALLOW'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'registered' => Loc::getMessage('C_REVIEWS_TEMPLATE_2_POST_ALLOW_REGISTERED'),
            'all' => Loc::getMessage('C_REVIEWS_TEMPLATE_2_POST_ALLOW_ALL')
        ],
        'DEFAULT' => 'registered'
    ];
}

$arTemplateParameters['DATE_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_2_DATE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DATE_SHOW'] === 'Y') {
    $arTemplateParameters['DATE_FORMAT'] = CIBlockParameters::GetDateFormat(
        Loc::getMessage('C_REVIEWS_TEMPLATE_2_DATE_FORMAT'),
        'VISUAL'
    );
}

$arTemplateParameters['CONSENT_URL'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_REVIEWS_TEMPLATE_2_CONSENT_URL'),
    'TYPE' => 'STRING',
    'DEFAULT' => '#SITE_DIR#company/consent/'
];