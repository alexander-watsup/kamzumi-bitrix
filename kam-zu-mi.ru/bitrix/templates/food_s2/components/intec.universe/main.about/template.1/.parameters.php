<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [
    'SETTINGS_USE' => [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_SETTINGS_USE'),
        'TYPE' => 'CHECKBOX'
    ],
    'LAZYLOAD_USE' => [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ],
    'PICTURE_SIZE' => [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PICTURE_SIZE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'contain' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PICTURE_SIZE_CONTAIN'),
            'cover' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PICTURE_SIZE_COVER')
        ],
        'DEFAULT' => 'cover'
    ],
    'BACKGROUND_SHOW' => [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_BACKGROUND_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ],
    'POSITION_HORIZONTAL' => [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_HORIZONTAL'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ],
    'POSITION_VERTICAL' => [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_VERTICAL'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'top' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_TOP'),
            'center' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_CENTER'),
            'bottom' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_POSITION_BOTTOM')
        ],
        'DEFAULT' => 'center'
    ],
];

if (!empty($arCurrentValues['IBLOCK_ID']) && $arCurrentValues['BACKGROUND_SHOW'] === 'Y') {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]));

    $hPropertyFile = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'F' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N')
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyFile = $arProperties->asArray($hPropertyFile);

    $arTemplateParameters['PROPERTY_BACKGROUND'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_PROPERTY_BACKGROUND'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyFile,
        'ADDITIONAL_VALUES' => 'Y'
    ];
}