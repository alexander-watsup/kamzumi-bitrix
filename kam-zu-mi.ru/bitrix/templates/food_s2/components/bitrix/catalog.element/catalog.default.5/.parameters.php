<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;
$bOffers = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

if (!empty($_REQUEST['site']))
    $sSite = $_REQUEST['site'];
else if (!empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arIBlocksType = CIBlockParameters::GetIBlockTypes();
$arIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], [
    'ACTIVE' => 'Y',
    'SITE_ID' => $sSite
]))->indexBy('ID');

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['MAIN_VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_MAIN_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        '1' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_MAIN_VIEW_1'),
        '2' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_MAIN_VIEW_2'),
        '3' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_MAIN_VIEW_3')
    ],
    'DEFAULT' => '1',
    'REFRESH' => 'Y'
];

if ($bBase) {
    $arTemplateParameters['DELAY_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DELAY_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));

    $hPropertyTextSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N' && empty($arValue['USER_TYPE']))
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyLinkedSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'E' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyLinkedMultiple = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'E' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'Y')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyCheckboxSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'L' && $arValue['LIST_TYPE'] === 'C' && $arValue['MULTIPLE'] === 'N')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyFileAll = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'F' && $arValue['LIST_TYPE'] === 'L')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
    $arPropertyLinkedSingle = $arProperties->asArray($hPropertyLinkedSingle);
    $arPropertyLinkedMultiple = $arProperties->asArray($hPropertyLinkedMultiple);
    $arPropertyCheckboxSingle = $arProperties->asArray($hPropertyCheckboxSingle);
    $arPropertyFileAll = $arProperties->asArray($hPropertyFileAll);

    $arTemplateParameters['PROPERTY_ARTICLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_ARTICLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_BRAND'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_BRAND'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyLinkedSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_MARKS_HIT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_MARKS_HIT'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyCheckboxSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_MARKS_NEW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_MARKS_NEW'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyCheckboxSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_MARKS_RECOMMEND'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_MARKS_RECOMMEND'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyCheckboxSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_PICTURES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_PICTURES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyFileAll,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_ORDER_USE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_ORDER_USE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyCheckboxSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_DOCUMENTS'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_DOCUMENTS'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyFileAll,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_ARTICLES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_ARTICLES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyLinkedMultiple,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_VIDEO'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_VIDEO'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyLinkedMultiple,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_VIDEO'])) {
        if (!empty($arCurrentValues['VIDEO_IBLOCK_TYPE']))
            $arVideoIBlocks = $arIBlocks->asArray(function ($key, $arIBlock) use (&$arCurrentValues) {
                if ($arIBlock['IBLOCK_TYPE_ID'] === $arCurrentValues['VIDEO_IBLOCK_TYPE'])
                    return [
                        'key' => $arIBlock['ID'],
                        'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                    ];

                return ['skip' => true];
            });
        else
            $arVideoIBlocks = $arIBlocks->asArray(function ($key, $arIBlock) {
                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            });

        $arTemplateParameters['VIDEO_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VIDEO_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksType,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
        $arTemplateParameters['VIDEO_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VIDEO_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arVideoIBlocks,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['VIDEO_IBLOCK_ID'])) {
            $arVideoProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
                'IBLOCK_ID' => $arCurrentValues['VIDEO_IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]));

            $arTemplateParameters['VIDEO_PROPERTY_URL'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VIDEO_PROPERTY_URL'),
                'TYPE' => 'LIST',
                'VALUES' => $arVideoProperties->asArray($hPropertyTextSingle),
                'ADDITIONAL_VALUES',
                'REFRESH' => 'Y'
            ];
        }
    }

    $arTemplateParameters['PROPERTY_ADDITIONAL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_ADDITIONAL'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyLinkedMultiple,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_ASSOCIATED'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_ASSOCIATED'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyLinkedMultiple,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_RECOMMENDED'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_RECOMMENDED'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyLinkedMultiple,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_SERVICES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_SERVICES'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyLinkedMultiple,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $sOffersId = null;
    $arOfferProperties = Arrays::from([]);

    if ($bBase) {
        $sOffersId = CCatalogSku::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);

        if (!empty($sOffersId['IBLOCK_ID'])) {
            $sOffersId = $sOffersId['IBLOCK_ID'];
            $bOffers = true;
        }
    } else if ($bLite) {
        $sOffersId = CStartShopCatalog::GetByIBlock($arCurrentValues['IBLOCK_ID'])->Fetch();

        if (!empty($sOffersId['OFFERS_IBLOCK'])) {
            $sOffersId = $sOffersId['OFFERS_IBLOCK'];
            $bOffers = true;
        }
    }

    if ($bOffers && !empty($sOffersId)) {
        $arOfferProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
            'IBLOCK_ID' => $sOffersId,
            'ACTIVE' => 'Y'
        ]));

        $arOfferPropertyTextSingle = $arOfferProperties->asArray($hPropertyTextSingle);
        $arOfferPropertyFileAll = $arOfferProperties->asArray($hPropertyFileAll);

        if (!empty($arCurrentValues['PROPERTY_ARTICLE'])) {
            $arTemplateParameters['OFFERS_PROPERTY_ARTICLE'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_OFFERS_PROPERTY_ARTICLE'),
                'TYPE' => 'LIST',
                'VALUES' => $arOfferPropertyTextSingle,
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
        }

        if (!empty($arCurrentValues['PROPERTY_PICTURES'])) {
            $arTemplateParameters['OFFERS_PROPERTY_PICTURES'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_OFFERS_PROPERTY_PICTURES'),
                'TYPE' => 'LIST',
                'VALUES' => $arOfferPropertyFileAll,
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
        }
    }
}

if (!empty($arCurrentValues['PROPERTY_ARTICLE'])) {
    $arTemplateParameters['ARTICLE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_ARTICLE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arTemplateParameters['VOTE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VOTE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['VOTE_SHOW'] === 'Y') {
        $arTemplateParameters['VOTE_TYPE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VOTE_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'rating' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VOTE_TYPE_RATING'),
                'vote_avg' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VOTE_TYPE_AVG')
            ],
            'DEFAULT' => 'rating'
        ];
    }
}

if (!empty($arCurrentValues['PROPERTY_BRAND'])) {
    $arTemplateParameters['BRAND_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_BRAND_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (
    !empty($arCurrentValues['PROPERTY_MARKS_HIT']) ||
    !empty($arCurrentValues['PROPERTY_MARKS_NEW']) ||
    !empty($arCurrentValues['PROPERTY_MARKS_RECOMMEND'])
) {
    $arTemplateParameters['MARKS_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_MARKS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['GALLERY_ACTION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_GALLERY_ACTION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_GALLERY_ACTION_NONE'),
        'source' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_GALLERY_ACTION_SOURCE'),
        'popup' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_GALLERY_ACTION_POPUP')
    ],
    'DEFAULT' => 'none'
];
$arTemplateParameters['GALLERY_ZOOM'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_GALLERY_ZOOM'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['GALLERY_PREVIEW_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_GALLERY_PREVIEW_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['SIZES_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SIZES_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SIZES_SHOW'] === 'Y') {
    $arTemplateParameters['SIZES_PATH'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SIZES_PATH'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['SIZES_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SIZES_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'all' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SIZES_MODE_ALL'),
            'element' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SIZES_MODE_ELEMENT')
        ],
        'DEFAULT' => 'all',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SIZES_MODE'] === 'element') {
        $arTemplateParameters['PROPERTY_SIZES_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTY_SIZES_SHOW'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyCheckboxSingle,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

$arTemplateParameters['SKU_VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SKU_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'dynamic' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SKU_VIEW_DYNAMIC'),
        'list' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SKU_VIEW_LIST')
    ],
    'DEFAULT' => 'dynamic',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SKU_VIEW'] === 'list') {
    $arTemplateParameters['SKU_NAME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SKU_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SKU_NAME_DEFAULT')
    ];
}

$arTemplateParameters['PROPERTIES_PREVIEW_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTIES_PREVIEW_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PROPERTIES_PREVIEW_SHOW'] === 'Y') {
    $arTemplateParameters['PROEPRTIES_PREVIEW_NAME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROEPRTIES_PREVIEW_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROEPRTIES_PREVIEW_NAME_DEFAULT')
    ];
    $arTemplateParameters['PROEPRTIES_PREVIEW_COUNT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROEPRTIES_PREVIEW_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '5'
    ];
}

$arTemplateParameters['RECALCULATION_PRICES_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_RECALCULATION_PRICES_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['PRICE_DISCOUNT_OLD'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRICE_DISCOUNT_OLD'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['PRICE_DISCOUNT_PERCENT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRICE_DISCOUNT_PERCENT'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PRICE_DISCOUNT_PERCENT'] === 'Y') {
    $arTemplateParameters['PRICE_DISCOUNT_ECONOMY'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRICE_DISCOUNT_ECONOMY'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['PRICE_RANGE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRICE_RANGE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if (!empty($arCurrentValues['PROPERTY_ADDITIONAL'])) {
    $arTemplateParameters['PRODUCTS_ADDITIONAL_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_ADDITIONAL_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['QUANTITY_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_QUANTITY_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['QUANTITY_SHOW'] === 'Y') {
    $arTemplateParameters['QUANTITY_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_QUANTITY_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'number' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_QUANTITY_MODE_NUMBER'),
            'text' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_QUANTITY_MODE_TEXT'),
            'logic' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_QUANTITY_MODE_LOGIC')
        ],
        'DEFAULT' => 'number',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['QUANTITY_MODE'] === 'text') {
        $arTemplateParameters['QUANTITY_BOUNDS_FEW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_QUANTITY_BOUNDS_FEW'),
            'TYPE' => 'STRING',
            'DEFAULT' => 10
        ];
        $arTemplateParameters['QUANTITY_BOUNDS_MANY'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_QUANTITY_BOUNDS_MANY'),
            'TYPE' => 'STRING',
            'DEFAULT' => 50
        ];
    }
}

if (Loader::includeModule('form'))
    include('parameters/base/forms.php');
else if (Loader::includeModule('intec.startshop'))
    include('parameters/lite/forms.php');
else
    return;

if ($bBase) {
    if ($arCurrentValues['SKU_VIEW'] === 'dynamic') {
        $arTemplateParameters['STORE_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => $arCurrentValues['QUANTITY_SHOW'] === 'Y' ? [
                'content' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_POSITION_CONTENT'),
                'popup' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_POSITION_POPUP')
            ] : [
                'content' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_POSITION_CONTENT')
            ],
            'DEFAULT' => 'content',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['STORE_POSITION'] !== 'popup') {
            $arTemplateParameters['STORE_NAME'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_NAME'),
                'TYPE' => 'STRING',
                'DEFUALT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_NAME_DEFAULT')
            ];
            $arTemplateParameters['STORE_TEMPLATE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_TEMPLATE'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'template.2' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_TEMPLATE_VIEW_1'),
                    'template.4' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_TEMPLATE_VIEW_2')
                ],
                'DEFAULT' => 1,
                'REFRESH' => 'Y'
            ];
        }
    } else {
        $arTemplateParameters['STORE_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'content' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STORE_POSITION_CONTENT')
            ],
            'DEFAULT' => 'content',
            'REFRESH' => 'Y'
        ];
    }

    include(__DIR__.'/parameters/stores.php');

    if ($arCurrentValues['SKU_VIEW'] === 'dynamic') {
        if ($arCurrentValues['STORE_POSITION'] !== 'popup') {
            $arTemplateParameters['STOREMAP_TEMPLATE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_STOREMAP_TEMPLATE'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'map.1' => '1'
                ],
                'DEFAULT' => 'map.1',
                'REFRESH' => 'Y'
            ];

            include(__DIR__.'/parameters/stores.map.php');
        }
    }

    include(__DIR__.'/parameters/delivery.calculation.php');
}

$arTemplateParameters['ACTION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_ACTION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_ACTION_NONE'),
        'buy' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_ACTION_BUY'),
        'order' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_ACTION_ORDER')
    ],
    'DEFAULT' => 'none',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ACTION'] === 'buy') {
    $arTemplateParameters['COUNTER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_COUNTER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['PRICE_INFO_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRICE_INFO_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PRICE_INFO_SHOW'] === 'Y') {
    $arTemplateParameters['PRICE_INFO_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRICE_INFO_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRICE_INFO_TEXT_DEFAULT')
    ];
}

if ($arCurrentValues['SKU_VIEW'] === 'dynamic') {
    $arTemplateParameters['PANEL_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PANEL_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['PANEL_MOBILE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PANEL_MOBILE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['SECTIONS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SECTIONS'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['DESCRIPTION_PREVIEW_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DESCRIPTION_PREVIEW_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['DESCRIPTION_DETAIL_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DESCRIPTION_DETAIL_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DESCRIPTION_DETAIL_SHOW'] === 'Y') {
    $arTemplateParameters['DESCRIPTION_DETAIL_NAME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DESCRIPTION_DETAIL_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DESCRIPTION_DETAIL_NAME_DEFAULT')
    ];
    $arTemplateParameters['DESCRIPTION_FROM_PREVIEW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DESCRIPTION_FROM_PREVIEW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ];
}

$arTemplateParameters['PROPERTIES_DETAIL_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTIES_DETAIL_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PROPERTIES_DETAIL_SHOW'] === 'Y') {
    $arTemplateParameters['PROPERTIES_DETAIL_NAME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTIES_DETAIL_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PROPERTIES_DETAIL_NAME_DEFAULT')
    ];
}

if (!empty($arCurrentValues['PROPERTY_DOCUMENTS'])) {
    $arTemplateParameters['DOCUMENTS_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DOCUMENTS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['DOCUMENTS_SHOW'] === 'Y') {
        $arTemplateParameters['DOCUMENTS_NAME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DOCUMENTS_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DOCUMENTS_NAME_DEFAULT')
        ];

        if ($arCurrentValues['MAIN_VIEW'] !== '2') {
            $arTemplateParameters['DOCUMENTS_POSITION'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DOCUMENTS_POSITION'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'content' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DOCUMENTS_POSITION_CONTENT'),
                    'column' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DOCUMENTS_POSITION_COLUMN')
                ],
                'DEFAULT' => 'content',
                'REFRESH' => 'Y'
            ];
        }

        if ($arCurrentValues['DOCUMENTS_POSITION'] === 'content') {
            $arTemplateParameters['DOCUMENTS_COLUMNS'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_DOCUMENTS_COLUMNS'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    3 => '3',
                    4 => '4'
                ],
                'DEFAULT' => 3
            ];
        }
    }
}

if (!empty($arCurrentValues['VIDEO_IBLOCK_TYPE']) && !empty($arCurrentValues['PROPERTY_VIDEO'])) {
    $arTemplateParameters['VIDEO_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VIDEO_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['VIDEO_SHOW'] === 'Y') {
        $arTemplateParameters['VIDEO_NAME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VIDEO_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_VIDEO_NAME_DEFAULT')
        ];

        include(__DIR__.'/parameters/videos.php');
    }
}

if (!empty($arCurrentValues['PROPERTY_ARTICLES'])) {
    $arTemplateParameters['ARTICLES_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_ARTICLES_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['ARTICLES_SHOW'] === 'Y') {
        $arTemplateParameters['ARTICLES_NAME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_ARTICLES_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_ARTICLES_NAME_DEFAULT')
        ];

        include(__DIR__ . '/parameters/articles.php');
    }
}

if (!empty($arCurrentValues['REVIEWS_IBLOCK_ID']) && !empty($arCurrentValues['REVIEWS_PROPERTY_ELEMENT_ID'])) {
    $arTemplateParameters['REVIEWS_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_REVIEW_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['REVIEWS_SHOW'] === 'Y') {
        $arTemplateParameters['REVIEWS_NAME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_REVIEWS_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_REVIEWS_NAME_DEFAULT')
        ];

        include(__DIR__.'/parameters/reviews.php');
    }
}

if (!empty($arCurrentValues['PROPERTY_BRAND'])) {
    $arTemplateParameters['BRAND_ADDITIONAL_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_BRAND_ADDITIONAL_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['BRAND_ADDITIONAL_SHOW'] === 'Y') {
        if ($arCurrentValues['MAIN_VIEW'] !== '2') {
            $arTemplateParameters['BRAND_ADDITIONAL_POSITION'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_BRAND_ADDITIONAL_POSITION'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'content' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_BRAND_ADDITIONAL_POSITION_CONTENT'),
                    'column' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_BRAND_ADDITIONAL_POSITION_COLUMN')
                ],
                'DEFAULT' => 'content'
            ];
        }

        $arTemplateParameters['BRAND_ADDITIONAL_LINK_NAME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_BRAND_ADDITIONAL_LINK_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_BRAND_ADDITIONAL_LINK_NAME_DEFAULT')
        ];
    }
}

if (!empty($arCurrentValues['PROPERTY_RECOMMENDED'])) {
    $arTemplateParameters['PRODUCTS_RECOMMENDED_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_RECOMMENDED_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PRODUCTS_RECOMMENDED_SHOW'] === 'Y') {
        $arTemplateParameters['PRODUCTS_RECOMMENDED_NAME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_RECOMMENDED_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_RECOMMENDED_NAME_DEFAULT')
        ];

        if ($arCurrentValues['MAIN_VIEW'] !== '2') {
            $arTemplateParameters['PRODUCTS_RECOMMENDED_POSITION'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_RECOMMENDED_POSITION'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'content' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_RECOMMENDED_POSITION_CONTENT'),
                    'column' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_RECOMMENDED_POSITION_COLUMN')
                ],
                'DEFAULT' => 'content',
                'REFRESH' => 'Y'
            ];
        }

        include(__DIR__ . '/parameters/products.recommended.php');
    }
}

if (!empty($arCurrentValues['PROPERTY_ASSOCIATED'])) {
    $arTemplateParameters['PRODUCTS_ASSOCIATED_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_ASSOCIATED_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PRODUCTS_ASSOCIATED_SHOW'] === 'Y') {
        $arTemplateParameters['PRODUCTS_ASSOCIATED_NAME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_ASSOCIATED_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_ASSOCIATED_NAME_DEFAULT')
        ];
        if ($arCurrentValues['MAIN_VIEW'] !== '2') {
            $arTemplateParameters['PRODUCTS_ASSOCIATED_POSITION'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_ASSOCIATED_POSITION'),
                'TYPE' => 'LIST',
                'VALUES' => [
                    'content' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_ASSOCIATED_POSITION_CONTENT'),
                    'column' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PRODUCTS_ASSOCIATED_POSITION_COLUMN')
                ],
                'DEFAULT' => 'content',
                'REFRESH' => 'Y'
            ];
        }

        include(__DIR__ . '/parameters/products.associated.php');
    }
}

if (!empty($arCurrentValues['PROPERTY_SERVICES'])) {
    $arTemplateParameters['SERVICES_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SERVICES_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SERVICES_SHOW'] === 'Y') {
        $arTemplateParameters['SERVICES_NAME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SERVICES_NAME'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_SERVICES_NAME_DEFAULT')
        ];

        include(__DIR__.'/parameters/services.php');
    }
}

$bShowColumn = false;

if ($arCurrentValues['BRAND_ADDITIONAL_POSITION'] === 'column' ||
    $arCurrentValues['PRODUCTS_RECOMMENDED_POSITION'] === 'column' ||
    $arCurrentValues['PRODUCTS_ASSOCIATED_POSITION'] === 'column'
) {
    $bShowColumn = true;
}

$arTemplateParameters['INFORMATION_BUY_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_BUY_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['INFORMATION_BUY_SHOW'] === 'Y') {
    $arTemplateParameters['INFORMATION_BUY_NAME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_BUY_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_BUY_NAME_DEFAULT')
    ];
    $arTemplateParameters['INFORMATION_BUY_PATH'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_BUY_PATH'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#SITE_DIR#include/catalog/information/buy.php'
    ];
    if ($bShowColumn) {
        $arTemplateParameters['INFORMATION_BUY_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_BUY_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'wide' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_BUY_POSITION_WIDE'),
                'column' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_BUY_POSITION_COLUMN')
            ],
            'DEFAULT' => 'wide'
        ];
    }
}

$arTemplateParameters['INFORMATION_PAYMENT_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_PAYMENT_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['INFORMATION_PAYMENT_SHOW'] === 'Y') {
    $arTemplateParameters['INFORMATION_PAYMENT_NAME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_PAYMENT_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_PAYMENT_NAME_DEFAULT')
    ];
    $arTemplateParameters['INFORMATION_PAYMENT_PATH'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_PAYMENT_PATH'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#SITE_DIR#include/catalog/information/payment.php'
    ];
    if ($bShowColumn) {
        $arTemplateParameters['INFORMATION_PAYMENT_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_PAYMENT_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'wide' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_PAYMENT_POSITION_WIDE'),
                'column' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_PAYMENT_POSITION_COLUMN')
            ],
            'DEFAULT' => 'wide'
        ];
    }
}

$arTemplateParameters['INFORMATION_SHIPMENT_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_SHIPMENT_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['INFORMATION_SHIPMENT_SHOW'] === 'Y') {
    $arTemplateParameters['INFORMATION_SHIPMENT_NAME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_SHIPMENT_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_SHIPMENT_NAME_DEFAULT')
    ];
    $arTemplateParameters['INFORMATION_SHIPMENT_PATH'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_SHIPMENT_PATH'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#SITE_DIR#include/catalog/information/shipment.php'
    ];
    if ($bShowColumn) {
        $arTemplateParameters['INFORMATION_SHIPMENT_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_SHIPMENT_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'wide' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_SHIPMENT_POSITION_WIDE'),
                'column' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_INFORMATION_SHIPMENT_POSITION_COLUMN')
            ],
            'DEFAULT' => 'wide'
        ];
    }
}

$arTemplateParameters['PURCHASE_BASKET_BUTTON_TEXT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PURCHASE_BASKET_BUTTON_TEXT'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PURCHASE_BASKET_BUTTON_TEXT_DEFAULT')
];
$arTemplateParameters['PURCHASE_ORDER_BUTTON_TEXT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PURCHASE_ORDER_BUTTON_TEXT'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_PURCHASE_ORDER_BUTTON_TEXT_DEFAULT')
];