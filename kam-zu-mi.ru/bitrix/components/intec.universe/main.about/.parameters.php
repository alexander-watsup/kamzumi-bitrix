<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$sSite = $_REQUEST['site'];

if (empty($sSite) && !empty($_REQUEST['src_site'])) {
    $sSite = $_REQUEST['src_site'];
}

$arIBlocksList = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
    'ACTIVE' => 'Y',
    'SITE_ID' => $sSite
]))->indexBy('ID');

if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $arIBlocksList = $arIBlocksList->asArray(function ($id, $value) use ($arCurrentValues) {
        if ($value['IBLOCK_TYPE_ID'] === $arCurrentValues['IBLOCK_TYPE'])
            return [
                'key' => $value['ID'],
                'value' => '['.$value['ID'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    });
} else {
    $arIBlocksList = $arIBlocksList->asArray(function ($id, $value) {
        return [
            'key' => $value['ID'],
            'value' => '['.$value['ID'].'] '.$value['NAME']
        ];
    });
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arSections = Arrays::fromDBResult(CIBlockSection::GetList(['SORT' => 'ASC'], [
        'GLOBAL_ACTIVE' => 'Y',
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('ID');

    if ($arCurrentValues['SECTIONS_MODE'] === 'code') {
        $arSections = $arSections->asArray(function ($id, $value) {
            if (!empty($value['CODE']))
                return [
                    'key' => $value['CODE'],
                    'value' => '['.$value['CODE'].'] '.$value['NAME']
                ];

            return ['skip' => true];
        });
    } else {
        $arSections = $arSections->asArray(function ($id, $value) {
            return [
                'key' => $value['ID'],
                'value' => '['.$value['ID'].'] '.$value['NAME']
            ];
        });
    }

    $arElementsFilter = [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ];

    if (!empty($arCurrentValues['SECTION'])) {
        if ($arCurrentValues['SECTIONS_MODE'] === 'code') {
            $arElementsFilter['SECTION_CODE'] = $arCurrentValues['SECTION'];
        } else {
            $arElementsFilter['SECTION_ID'] = $arCurrentValues['SECTION'];
        }
    }

    $arElements = Arrays::fromDBResult(CIBlockElement::GetList(
        ['SORT' => 'ASC'],
        $arElementsFilter
    ))->indexBy('ID');

    if ($arCurrentValues['ELEMENTS_MODE'] === 'code') {
        $arElements = $arElements->asArray(function ($id, $value) {
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['NAME']
            ];
        });
    } else {
        $arElements = $arElements->asArray(function ($id, $value) {
            return [
                'key' => $value['ID'],
                'value' => '['.$value['ID'].'] '.$value['NAME']
            ];
        });
    }

    if (!empty($arCurrentValues['ELEMENT'])) {
        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ]));

        $hPropertyText = function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
                ];

            return ['skip' => true];
        };

        $arPropertyText = $arProperties->asArray($hPropertyText);
    }
}

$arParameters = [];

$arParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetIBlockTypes(),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksList,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arParameters['SECTIONS_MODE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_SECTIONS_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'id' => Loc::getMessage('C_MAIN_ABOUT_SECTIONS_MODE_ID'),
            'code' => Loc::getMessage('C_MAIN_ABOUT_SECTIONS_MODE_CODE')
        ],
        'DEFAULT' => 'id',
        'REFRESH' => 'Y'
    ];
    $arParameters['SECTION'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_SECTION'),
        'TYPE' => 'LIST',
        'VALUES' => $arSections,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arParameters['ELEMENTS_MODE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_ELEMENTS_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'id' => Loc::getMessage('C_MAIN_ABOUT_ELEMENTS_MODE_ID'),
            'code' => Loc::getMessage('C_MAIN_ABOUT_ELEMENTS_MODE_CODE')
        ],
        'DEFAULT' => 'id',
        'REFRESH' => 'Y'
    ];
    $arParameters['ELEMENT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ABOUT_ELEMENT'),
        'TYPE' => 'LIST',
        'VALUES' => $arElements,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['ELEMENT'])) {
        $arParameters['PICTURE_SOURCES'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_PICTURE_SOURCES'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'preview' => Loc::getMessage('C_MAIN_ABOUT_PICTURE_SOURCES_PREVIEW'),
                'detail' => Loc::getMessage('C_MAIN_ABOUT_PICTURE_SOURCES_DETAIL')
            ],
            'MULTIPLE' => 'Y',
            'SIZE' => 5,
            'REFRESH' => 'Y'
        ];

        $arParameters['BUTTON_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_BUTTON_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['BUTTON_SHOW'] === 'Y') {
            $arParameters['BUTTON_TEXT'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_BUTTON_TEXT'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('C_MAIN_ABOUT_BUTTON_TEXT_DEFAULT'),
            ];

            $arParameters['PROPERTY_LINK'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_PROPERTY_LINK'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyText,
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }

        $arParameters['TITLE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_TITLE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['TITLE_SHOW']) {
            $arParameters['PROPERTY_TITLE'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_MAIN_ABOUT_PROPERTY_TITLE'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertyText,
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }

        $arParameters['PROPERTY_VIDEO'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_PROPERTY_VIDEO'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyText,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

$arParameters['SORT_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_SORT_BY'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetElementSortFields(),
    'DEFAULT' => 'SORT',
    'ADDITIONAL_VALUES' => 'Y'
];

$arParameters['ORDER_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_MAIN_ABOUT_ORDER_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ASC' => Loc::getMessage('C_MAIN_ABOUT_ORDER_BY_ASC'),
        'DESC' => Loc::getMessage('C_MAIN_ABOUT_ORDER_BY_DESC')
    ],
    'DEFAULT' => 'ASC'
];

$arComponentParameters = [
    'GROUPS' => [
        'SORT' => [
            'NAME' => Loc::getMessage('C_MAIN_ABOUT_GROUP_SORT'),
            'SORT' => 800
        ]
    ],
    'PARAMETERS' => $arParameters
];