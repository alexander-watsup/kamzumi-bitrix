<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

$bBase = false;
$bLite = false;

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([], ['ACTIVE' => 'Y']))->indexBy('ID');
$arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);

$arTemplateParameters = [];

if ($bLite) {
    $arPriceCodes = Arrays::fromDBResult(CStartShopPrice::GetList())->indexBy('CODE');

    $hPriceCodes = function ($sKey, $arProperty) {
        if (!empty($arProperty['CODE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['LANG'][LANGUAGE_ID]['NAME']
            ];

        return ['skip' => true];
    };

    $arTemplateParameters['PRICE_CODE'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_PRICE_CODE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPriceCodes->asArray($hPriceCodes),
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['CONVERT_CURRENCY'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_CONVERT_CURRENCY'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['CONVERT_CURRENCY'] === 'Y') {
        $arCurrencies = Arrays::fromDBResult(CStartShopCurrency::GetList())->indexBy('CODE');

        $hCurrencies = function ($sKey, $arProperty) {
            if (!empty($arProperty['CODE']))
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '['.$arProperty['CODE'].'] '.$arProperty['LANG'][LANGUAGE_ID]['NAME']
                ];

            return ['skip' => true];
        };

        $arTemplateParameters['CURRENCY_ID'] = [
            'PARENT' => 'PRICES',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_CURRENCY_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arCurrencies->asArray($hCurrencies),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['ACTION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_ACTION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_ACTION_NONE'),
        'buy' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_ACTION_BUY'),
        'detail' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_ACTION_DETAIL'),
        'order' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_ACTION_ORDER')
    ],
    'DEFAULT' => 'buy',
    'REFRESH' => 'Y'
];

$arTemplateParameters['BORDERS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_BORDERS'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => 2,
        3 => 3,
        4 => 4
    ],
    'DEFAULT' => 3
];

$arTemplateParameters['COLUMNS_MOBILE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_COLUMNS_MOBILE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        1 => 1,
        2 => 2
    ],
    'DEFAULT' => 1
];

$arTemplateParameters['IMAGE_ASPECT_RATIO'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_IMAGE_ASPECT_RATIO'),
    'TYPE' => 'LIST',
    'VALUES' => [
        '1:1' => '1:1',
        '4:5' => '4:5',
        '3:4' => '3:4',
        '5:7' => '5:7',
        '4:6' => '4:6',
        '3:5' => '3:5'
    ],
    'ADDITIONAL_VALUES' => 'Y',
    'DEFAULT' => '1:1'
];

$arTemplateParameters['RECALCULATION_PRICES_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_RECALCULATION_PRICES_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['COUNTER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_COUNTER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

$arTemplateParameters['OFFERS_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_OFFERS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];

$arTemplateParameters['CONSENT_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_CONSENT_URL'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['LAZY_LOAD'] = [
    'PARENT' => 'PAGER_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_LAZY_LOAD'),
    'TYPE' => 'CHECKBOX'
];

if (!empty($arIBlock)) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arIBlock['ID']
    ]))->indexBy('ID');

    $arTemplateParameters['PROPERTY_ORDER_USE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_PROPERTY_ORDER_USE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_MARKS_RECOMMEND'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_PROPERTY_MARKS_RECOMMEND'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_MARKS_NEW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_PROPERTY_MARKS_NEW'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_MARKS_HIT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_PROPERTY_MARKS_HIT'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

if ($bBase) {
    $arTemplateParameters['DELAY_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_DELAY_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['VOTE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_VOTE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['VOTE_SHOW'] === 'Y') {
    $arTemplateParameters['VOTE_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_VOTE_MODE_RATING'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'rating' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_VOTE_MODE_RATING'),
            'average' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_VOTE_MODE_AVERAGE')
        ],
        'DEFAULT' => 'rating'
    ];
}

$arTemplateParameters['QUANTITY_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_QUANTITY_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['QUANTITY_SHOW'] === 'Y') {
    $arTemplateParameters['QUANTITY_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_QUANTITY_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'number' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_QUANTITY_MODE_NUMBER'),
            'text' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_QUANTITY_MODE_TEXT'),
            'logic' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_QUANTITY_MODE_LOGIC')
        ],
        'DEFAULT' => 'number',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['QUANTITY_MODE'] === 'text') {
        $arTemplateParameters['QUANTITY_BOUNDS_FEW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_QUANTITY_BOUNDS_FEW'),
            'TYPE' => 'STRING',
        ];
        $arTemplateParameters['QUANTITY_BOUNDS_MANY'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_QUANTITY_BOUNDS_MANY'),
            'TYPE' => 'STRING',
        ];
    }
}

include(__DIR__.'/parameters/quick.view.php');

if (Loader::includeModule('form')) {
    include(__DIR__.'/parameters/base/forms.php');
} else if (Loader::includeModule('intec.startshop')) {
    include(__DIR__.'/parameters/lite/forms.php');
}