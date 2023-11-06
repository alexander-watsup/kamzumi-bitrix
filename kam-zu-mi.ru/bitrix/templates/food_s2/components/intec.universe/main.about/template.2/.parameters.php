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

$arTemplateParameters['VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        1 => '1',
        2 => '2',
        3 => '3',
    ],
    'DEFAULT' => 1,
    'REFRESH' => 'Y'
];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['PICTURE_SIZE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PICTURE_SIZE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'contain' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PICTURE_SIZE_CONTAIN'),
        'cover' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PICTURE_SIZE_COVER')
    ],
    'DEFAULT' => 'cover'
];
$arTemplateParameters['POSITION_HORIZONTAL'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_HORIZONTAL'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_LEFT'),
        'center' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_CENTER'),
        'right' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_RIGHT')
    ],
    'DEFAULT' => 'center'
];
$arTemplateParameters['POSITION_VERTICAL'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_VERTICAL'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'top' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_TOP'),
        'center' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_CENTER'),
        'bottom' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_POSITION_BOTTOM')
    ],
    'DEFAULT' => 'center'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('ID');

    $hPropertyElements = function ($sKey, $arProperty) {
        if (($arProperty['PROPERTY_TYPE'] === 'E' || $arProperty['PROPERTY_TYPE'] === 'E') && $arProperty['MULTIPLE'] === 'Y')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyElements = $arProperties->asArray($hPropertyElements);

    $arTemplateParameters['PROPERTY_ADVANTAGES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_PROPERTY_ADVANTAGES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyElements,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

$sSite = $_REQUEST['site'];

if (empty($sSite) && !empty($_REQUEST['src_site'])) {
    $sSite = $_REQUEST['src_site'];
}

$arTemplateParameters['ADVANTAGES_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['VIEW'] != 3 && $arCurrentValues['ADVANTAGES_SHOW']) {

    $arIBlocksList = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'SITE_ID' => $sSite
    ]))->indexBy('ID');

    if (!empty($arCurrentValues['ADVANTAGES_IBLOCK_TYPE'])) {
        $arIBlocksList = $arIBlocksList->asArray(function ($id, $value) use ($arCurrentValues) {
            if ($value['IBLOCK_TYPE_ID'] === $arCurrentValues['ADVANTAGES_IBLOCK_TYPE'])
                return [
                    'key' => $value['ID'],
                    'value' => '[' . $value['ID'] . '] ' . $value['NAME']
                ];

            return ['skip' => true];
        });
    } else {
        $arIBlocksList = $arIBlocksList->asArray(function ($id, $value) {
            return [
                'key' => $value['ID'],
                'value' => '[' . $value['ID'] . '] ' . $value['NAME']
            ];
        });
    }

    if (!empty($arCurrentValues['ADVANTAGES_IBLOCK_ID'])) {
        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['ADVANTAGES_IBLOCK_ID']
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

        $arTemplateParameters['ADVANTAGES_PROPERTY_SVG_FILE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_PROPERTY_SVG_FILE'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyFile,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    $arTemplateParameters['ADVANTAGES_IBLOCK_TYPE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => CIBlockParameters::GetIBlockTypes(),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['ADVANTAGES_IBLOCK_ID'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocksList,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['ADVANTAGES_COLUMNS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_ADVANTAGES_COLUMNS'),
        'TYPE' => 'LIST',
        'VALUES' => [
            2 => '2',
            3 => '3',
        ],
        'DEFAULT' => 2
    ];

}

$arTemplateParameters['BUTTON_VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_2_BUTTON_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        1 => '1',
        2 => '2',
    ],
    'DEFAULT' => 1
];