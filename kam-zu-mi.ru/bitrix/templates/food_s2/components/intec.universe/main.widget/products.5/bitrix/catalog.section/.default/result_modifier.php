<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

$bSeo = Loader::includeModule('intec.seo');

$arParams = ArrayHelper::merge([
    'ACTION' => 'buy',
    'BUTTON_TOGGLE_ACTION' => 'buy',
    'PROPERTIES_SHOW' => 'Y',
    'COUNTER_SHOW' => 'Y',
    'VOTE_SHOW' => 'N',
    'VOTE_MODE' => 'rating',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => 3,
    'QUANTITY_BOUNDS_MANY' => 10,
    'FORM_ID' => null,
    'FORM_TEMPLATE' => null,
    'FORM_PROPERTY_PRODUCT' => null,
    'OFFERS_USE' => 'Y',
    'QUICK_VIEW_USE' => 'N',
    'QUICK_VIEW_TEMPLATE' => null,
    'QUICK_VIEW_DETAIL' => 'N',
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_ORDER_USE' => null,
    'LAZY_LOAD' => 'N',
    'COMPARE_SHOW_INACTIVE' => 'N',
    'DELAY_SHOW_INACTIVE' => 'N',
    'RECALCULATION_PRICES_USE' => 'N',
    'MEASURE_SHOW' => 'N',
    'USE_PRICE_COUNT' => false,
    'BLOCKS_HEADER_SHOW' => 'N',
    'BLOCKS_HEADER_TEXT' => null,
    'BLOCKS_HEADER_ALIGN' => 'left',
    'BLOCKS_DESCRIPTION_SHOW' => 'N',
    'BLOCKS_DESCRIPTION_TEXT' => null,
    'BLOCKS_DESCRIPTION_ALIGN' => 'left',
    'VIEW' => 'tabs',
    'SECTIONS_TITLE_SHOW' => 'Y',
    'SECTIONS_TITLE_ALIGN' => 'center'
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$arPosition = [
    'left',
    'center',
    'right'
];

$arBlocks = [
    'HEADER' => [
        'SHOW' => $arParams['BLOCKS_HEADER_SHOW'] === 'Y',
        'TEXT' => $arParams['~BLOCKS_HEADER_TEXT'],
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['BLOCKS_HEADER_ALIGN'])
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['BLOCKS_DESCRIPTION_SHOW'] === 'Y',
        'TEXT' => $arParams['~BLOCKS_DESCRIPTION_TEXT'],
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['BLOCKS_DESCRIPTION_ALIGN'])
    ]
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y'
    ],
    'PROPERTIES' => [
        'SHOW' => $arParams['PROPERTIES_SHOW'] === 'Y',
        'AMOUNT' => ArrayHelper::fromRange([5, 0, 1, 2, 3, 4, 6], $arParams['PROPERTIES_AMOUNT'])
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y'
    ],
    'NAVIGATION' => [
        'TOP' => [
            'SHOW' => $arParams['DISPLAY_TOP_PAGER']
        ],
        'BOTTOM' => [
            'SHOW' => $arParams['DISPLAY_BOTTOM_PAGER']
        ],
        'LAZY' => [
            'BUTTON' => $arParams['LAZY_LOAD'] === 'Y',
            'SCROLL' => $arParams['LOAD_ON_SCROLL'] === 'Y'
        ]
    ],
    'OFFERS' => [
        'USE' => $arParams['OFFERS_USE'] === 'Y'
    ],
    'VOTE' => [
        'SHOW' => $arParams['VOTE_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange([
            'average',
            'rating'
        ], $arParams['VOTE_MODE'])
    ],
    'QUANTITY' => [
        'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange([
            'number',
            'text',
            'logic'
        ], $arParams['QUANTITY_MODE']),
        'BOUNDS' => [
            'FEW' => Type::toFloat($arParams['QUANTITY_BOUNDS_FEW']),
            'MANY' => Type::toFloat($arParams['QUANTITY_BOUNDS_MANY'])
        ]
    ],
    'BUTTON_TOGGLE' => [
        'ACTION' => ArrayHelper::fromRange([
            'none',
            'buy'
        ], $arParams['BUTTON_TOGGLE_ACTION'])
    ],
    'PRICE' => [
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y',
        'RANGE' => $arParams['USE_PRICE_COUNT']
    ],
    'VIEW' => ArrayHelper::fromRange(['tabs', 'sections'], $arParams['VIEW']),
    'TABS' => [
        'ALIGN' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['TABS_ALIGN'])
    ],
    'SECTIONS' => [
        'TITLE' => [
            'SHOW' => $arParams['SECTIONS_TITLE_SHOW'] === 'Y',
            'ALIGN' => ArrayHelper::fromRange([
                'left',
                'center',
                'right'
            ], $arParams['SECTIONS_TITLE_ALIGN'])
        ]
    ],
    'MEASURE' => [
        'SHOW' => $arParams['MEASURE_SHOW'] === 'Y'
    ],
    'BUTTONS' => [
        'BASKET' => [
            'TEXT' => $arParams['PURCHASE_BASKET_BUTTON_TEXT']
        ]
    ]
];

if (empty($arVisual['BUTTONS']['BASKET']['TEXT']))
    $arVisual['BUTTONS']['BASKET']['TEXT'] = Loc::getMessage('C_WIDGET_PRODUCTS_5_BUTTON_ADD');

if (defined('EDITOR') || !class_exists('\\intec\\template\\Properties'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['QUANTITY']['BOUNDS']['FEW'] < 0)
    $arVisual['QUANTITY']['BOUNDS']['FEW'] = 3;

if ($arVisual['QUANTITY']['BOUNDS']['MANY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW'])
    $arVisual['QUANTITY']['BOUNDS']['MANY'] = $arVisual['QUANTITY']['BOUNDS']['FEW'] + 1;

if (empty($arResult['NAV_STRING'])) {
    $arVisual['NAVIGATION']['TOP']['SHOW'] = false;
    $arVisual['NAVIGATION']['BOTTOM']['SHOW'] = false;
}

if (!$arVisual['PROPERTIES']['SHOW'])
    $arVisual['PROPERTIES']['AMOUNT'] = 0;

$arVisual['PROPERTIES']['COLUMNS'] = $arVisual['PROPERTIES']['AMOUNT'] - 2;
$arVisual['PROPERTIES']['COLUMNS'] = $arVisual['PROPERTIES']['COLUMNS'] > 0 ? $arVisual['PROPERTIES']['COLUMNS'] : 1;

$arResult['ACTION'] = ArrayHelper::fromRange([
    'none',
    'buy',
    'detail',
    'order'
], $arParams['ACTION']);

$arResult['DELAY'] = [
    'USE' => $arParams['DELAY_USE'] === 'Y',
    'SHOW_INACTIVE' => $arParams['DELAY_SHOW_INACTIVE'] === 'Y'
];

if ($arResult['ACTION'] !== 'buy' || $bLite)
    $arResult['DELAY']['USE'] = false;

$arResult['COMPARE'] = [
    'USE' => $arParams['USE_COMPARE'] === 'Y',
    'CODE' => $arParams['COMPARE_NAME'],
    'SHOW_INACTIVE' => $arParams['COMPARE_SHOW_INACTIVE'] === 'Y'
];

if (empty($arResult['COMPARE']['CODE']))
    $arResult['COMPARE']['USE'] = false;

$arResult['FORM'] = [
    'SHOW' => true,
    'ID' => $arParams['FORM_ID'],
    'TEMPLATE' => $arParams['FORM_TEMPLATE'],
    'PROPERTIES' => [
        'PRODUCT' => $arParams['FORM_PROPERTY_PRODUCT']
    ]
];

if (empty($arResult['FORM']['ID']))
    $arResult['FORM']['SHOW'] = false;

$arResult['URL'] = [
    'BASKET' => ArrayHelper::getValue($arParams, 'BASKET_URL'),
    'CONSENT' => ArrayHelper::getValue($arParams, 'CONSENT_URL')
];

foreach ($arResult['URL'] as $sKey => $sUrl)
    $arResult['URL'][$sKey] = StringHelper::replaceMacros($sUrl, $arMacros);

if ($bSeo) {
    $arSeo = $APPLICATION->IncludeComponent('intec.seo:iblocks.elements.modifier', '', [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SECTION_ID' => $arParams['SECTION_ID'],
        'SECTION_CODE' => $arParams['SECTION_CODE'],
        'ITEMS' => $arResult['ITEMS'],
        'PAGE_USE' => 'Y',
        'PAGE_COUNT' => !empty($arResult['NAV_RESULT']) ? $arResult['NAV_RESULT']->NavPageCount : null,
        'PAGE_SIZE' => !empty($arResult['NAV_RESULT']) ? $arResult['NAV_RESULT']->NavPageSize : null,
        'PAGE_NUMBER' => !empty($arResult['NAV_RESULT']) ? $arResult['NAV_RESULT']->NavPageNomer : null,
        'CACHE_TYPE' => 'N'
    ], $component);

    if (!empty($arSeo))
        $arResult['ITEMS'] = $arSeo['ITEMS'];
}

foreach ($arResult['ITEMS'] as $iKey => &$arItem) {
    $arItem['ACTION'] = $arResult['ACTION'];
    $arItem['DELAY'] = [
        'USE' => $arResult['DELAY']['USE']
    ];

    $arOrderUse = ArrayHelper::getValue($arItem, [
        'PROPERTIES',
        $arParams['PROPERTY_ORDER_USE'],
        'VALUE'
    ]);

    if (!empty($arOrderUse) && $arItem['ACTION'] === 'buy' && !empty($arResult['FORM']['ID'])) {
        $arItem['ACTION'] = 'order';
        $arItem['DELAY']['USE'] = false;
    }

    $arItem['PICTURE'] = null;

    if (!empty($arItem['PREVIEW_PICTURE'])) {
        $arItem['PICTURE'] = $arItem['PREVIEW_PICTURE'];
    } else if (!empty($arItem['DETAIL_PICTURE'])) {
        $arItem['PICTURE'] = $arItem['DETAIL_PICTURE'];
    }

    $arItem['MARKS'] = [
        'RECOMMEND' => ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_MARKS_RECOMMEND'],
            'VALUE'
        ]),
        'NEW' => ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_MARKS_NEW'],
            'VALUE'
        ]),
        'HIT' => ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_MARKS_HIT'],
            'VALUE'
        ])
    ];

    $arItem['MARKS']['RECOMMEND'] = !empty($arItem['MARKS']['RECOMMEND']);
    $arItem['MARKS']['NEW'] = !empty($arItem['MARKS']['NEW']);
    $arItem['MARKS']['HIT'] = !empty($arItem['MARKS']['HIT']);

    unset($arItem);
}

if ($bLite)
    include(__DIR__ . '/modifiers/lite/catalog.php');

include(__DIR__.'/modifiers/quick.view.php');

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['BLOCKS'] = $arBlocks;
$arResult['VISUAL'] = $arVisual;
$arResult['MODE'] = $arParams['MODE'];

$arCategories = [];
$arSections = [];
$arProperty = $arParams['PROPERTY_CATEGORY'];

if (!empty($arProperty)) {
    $arProperty = CIBlockProperty::GetList([], [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'CODE' => $arProperty
    ])->GetNext();
}

if (!empty($arProperty)) {
    $rsCategories = CIBlockPropertyEnum::GetList(['SORT' => 'ASC'], [
        'PROPERTY_ID' => $arProperty['ID']
    ]);

    while ($arCategory = $rsCategories->GetNext()) {
        if (empty($arCategory['XML_ID']))
            continue;

        $arCategories[$arCategory['XML_ID']] = [
            'CODE' => $arCategory['XML_ID'],
            'NAME' => $arCategory['VALUE'],
            'SORT' => $arCategory['SORT'],
            'PROPERTIES' => [],
            'ITEMS' => []
        ];
    }

    unset($arCategory);
    unset($rsCategories);
} else {
    $arResult['ITEMS'] = [];
}

foreach ($arResult['ITEMS'] as &$arItem) {
    if (!empty($arItem['IBLOCK_SECTION_ID']))
        $arSections[] = $arItem['IBLOCK_SECTION_ID'];

    $arCategory = ArrayHelper::getValue($arItem, [
        'PROPERTIES',
        $arProperty['CODE'],
        'VALUE_XML_ID'
    ]);

    if (!empty($arCategory) && ArrayHelper::keyExists($arCategory, $arCategories)) {
        $arCategory = &$arCategories[$arCategory];
        $arCategory['ITEMS'][] = &$arItem;

        unset($arCategory);
    }
}

if (!empty($arSections)) {
    $rsSections = CIBlockSection::GetList([
        'SORT' => 'ASC'
    ], [
        'ID' => $arSections,
        'IBLOCK_ID' => $arParams['IBLOCK_ID']
    ]);

    $arSections = Arrays::from([]);
    $rsSections->SetUrlTemplates(
        $arParams['DETAIL_URL'],
        $arParams['SECTION_URL']
    );

    while ($arSection = $rsSections->GetNext())
        $arSections->set($arSection['ID'], $arSection);

    unset($arSection);
    unset($rsSections);
}

$arSections = Arrays::from($arSections);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['SECTION'] = $arSections->get($arItem['IBLOCK_SECTION_ID']);

    unset($arItem);
}

$arResult['CATEGORIES'] = [];

foreach ($arCategories as $sKey => $arCategory)
    if (!empty($arCategory['ITEMS']))
        $arResult['CATEGORIES'][$sKey] = $arCategory;

include(__DIR__.'/modifiers/properties.php');

unset($arCategory);
unset($arCategories);
unset($arBlocks);
unset($arVisual);