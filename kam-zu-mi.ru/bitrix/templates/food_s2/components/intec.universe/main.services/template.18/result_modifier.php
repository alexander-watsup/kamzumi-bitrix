<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
    'PROPERTY_PRICE' => null,
    'PROPERTY_PRICE_OLD' => null,
    'PROPERTY_CURRENCY' => null,
    'PROPERTY_PRICE_FORMAT' => null,
    'COLUMNS' => 3,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PICTURE_SHOW' => 'N',
    'PRICE_SHOW' => 'N',
    'PRICE_OLD_SHOW' => 'N',
    'PRICE_FORMAT' => null,
    'SLIDER_USE' => 'N',
    'SLIDER_LOOP' => 'N',
    'SLIDER_NAV_SHOW' => 'N',
    'SLIDER_NAV_VIEW' => 'default',
    'SLIDER_AUTO_USE' => 'N',
    'SLIDER_AUTO_TIME' => 10000,
    'SLIDER_AUTO_HOVER' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([3, 4], $arParams['COLUMNS']),
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PICTURE' => [
        'SHOW' => $arParams['PICTURE_SHOW'] === 'Y',
        'SIZE' => $arParams['COLUMNS'] > 3 ? 'small' : 'default'
    ],
    'PRICE' => [
        'SHOW' => $arParams['PRICE_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRICE']),
        'FORMAT' => $arParams['PRICE_FORMAT']
    ],
    'PRICE_OLD' => [
      'SHOW' => $arParams['PRICE_OLD_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRICE_OLD']),
      'FORMAT' => $arParams['PRICE_FORMAT']
    ],
    'SLIDER' => [
        'USE' => $arParams['SLIDER_USE'] === 'Y' && count($arResult['ITEMS']) > $arParams['COLUMNS'],
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'NAV' => [
            'SHOW' => $arParams['SLIDER_NAV_SHOW'] === 'Y',
            'VIEW' => ArrayHelper::fromRange(['default', 'top'], $arParams['SLIDER_NAV_VIEW'])
        ],
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTO_USE'] === 'Y',
            'TIME' => Type::toInteger($arParams['SLIDER_AUTO_TIME']),
            'HOVER' => $arParams['SLIDER_AUTO_HOVER'] === 'Y'
        ]
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PICTURE' => [],
        'PRICE' => [
            'BASE' => [
                'SHOW' => false,
                'VALUE' => null,
                'PRINT' => null
            ],
            'OLD' => [
                'SHOW' => false,
                'VALUE' => null,
                'PRINT' => null
            ],
            'CURRENCY' => null,
            'FORMAT' => null,
        ]
    ];

    if($arParams['PRICE_OLD_SHOW'] === 'Y')
        $arItem['DATA']['PRICE']['OLD']['SHOW'] = true;

    if (!empty($arItem['PREVIEW_PICTURE']))
        $arItem['DATA']['PICTURE'] = $arItem['PREVIEW_PICTURE'];
    else if (!empty($arItem['DETAIL_PICTURE']))
        $arItem['DATA']['PICTURE'] = $arItem['DETAIL_PICTURE'];

    if (!empty($arParams['PROPERTY_PRICE'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE']
        ]);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['PRICE']['BASE']['VALUE'] = number_format($arProperty['DISPLAY_VALUE'], 0, '', ' ');
            }
        }
    }
    if (!empty($arParams['PROPERTY_PRICE_OLD']) && $arItem['DATA']['PRICE']['OLD']['SHOW']) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE_OLD']
        ]);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['PRICE']['OLD']['VALUE'] = number_format($arProperty['DISPLAY_VALUE'], 0, '', ' ');

            }
        }
    }

    if (!empty($arParams['PROPERTY_CURRENCY'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_CURRENCY'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arItem['DATA']['PRICE']['CURRENCY'] = $arProperty;
        }
    }

    if (!empty($arParams['PROPERTY_PRICE_FORMAT'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_PRICE_FORMAT']
        ]);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['PRICE']['FORMAT'] = $arProperty['DISPLAY_VALUE'];
            }
        }
    }

    if (empty($arItem['DATA']['PRICE']['FORMAT']) && !empty($arVisual['PRICE']['FORMAT']))
        $arItem['DATA']['PRICE']['FORMAT'] = $arVisual['PRICE']['FORMAT'];

    if (!empty($arItem['DATA']['PRICE']['BASE']['VALUE'])) {
        if (!empty($arItem['DATA']['PRICE']['FORMAT'])) {
            $arItem['DATA']['PRICE']['BASE']['PRINT'] = trim(
                StringHelper::replaceMacros($arItem['DATA']['PRICE']['FORMAT'], [
                    'VALUE' => $arItem['DATA']['PRICE']['BASE']['VALUE'],
                    'CURRENCY' => $arItem['DATA']['PRICE']['CURRENCY']
                ])
            );
        } else {
            if (!empty($arItem['DATA']['PRICE']['CURRENCY']))
                $arItem['DATA']['PRICE']['BASE']['PRINT'] = $arItem['DATA']['PRICE']['BASE']['VALUE'].' '.$arItem['DATA']['PRICE']['CURRENCY'];
            else
                $arItem['DATA']['PRICE']['BASE']['PRINT'] = $arItem['DATA']['PRICE']['BASE']['VALUE'];
        }
    }
    if (!empty($arItem['DATA']['PRICE']['OLD']['VALUE'])) {
        if (!empty($arItem['DATA']['PRICE']['FORMAT'])) {
            $arItem['DATA']['PRICE']['OLD']['PRINT'] = trim(
                StringHelper::replaceMacros($arItem['DATA']['PRICE']['FORMAT'], [
                    'VALUE' => $arItem['DATA']['PRICE']['OLD']['VALUE'],
                    'CURRENCY' => $arItem['DATA']['PRICE']['CURRENCY']
                ])
            );
        } else {
            if (!empty($arItem['DATA']['PRICE']['CURRENCY']))
                $arItem['DATA']['PRICE']['OLD']['PRINT'] = $arItem['DATA']['PRICE']['OLD']['VALUE'].' '.$arItem['DATA']['PRICE']['CURRENCY'];
            else
                $arItem['DATA']['PRICE']['OLD']['PRINT'] = $arItem['DATA']['PRICE']['OLD']['VALUE'];
        }
    }
}

unset($arItem, $arProperty);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);