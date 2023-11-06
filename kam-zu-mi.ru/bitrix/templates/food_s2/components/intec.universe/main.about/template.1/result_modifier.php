<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'BUTTON_SHOW' => 'N',
    'BUTTON_TEXT' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_BUTTON_TEXT_DEFAULT'),
    'TITLE_SHOW' => 'N',
    'PICTURE_SIZE' => 'cover',
    'PICTURE_WIDE' => 'N',
    'POSITION_VERTICAL' => 'center',
    'POSITION_HORIZONTAL' => 'center',
    'BACKGROUND_SHOW' => 'N',
    'PROPERTY_BACKGROUND' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'PICTURE' => [
        'SIZE' => ArrayHelper::fromRange(['contain', 'cover'], $arParams['PICTURE_SIZE']),
        'WIDE' => $arParams['PICTURE_WIDE'] === 'Y',
        'POSITION' => [
            'VERTICAL' => ArrayHelper::fromRange(['top', 'center', 'bottom'], $arParams['POSITION_VERTICAL']),
            'HORIZONTAL' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['POSITION_HORIZONTAL'])
        ]
    ],
    'TITLE' => [
        'SHOW' => $arParams['TITLE_SHOW'] === 'Y'
    ],
    'BUTTON' => [
        'SHOW' => $arParams['BUTTON_SHOW'] === 'Y',
        'TEXT' => $arParams['BUTTON_TEXT'],
    ],
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'BACKGROUND' => [
        'SHOW' => $arParams['BACKGROUND_SHOW'] === 'Y'
    ]
];

if (empty($arResult['LINK']))
    $arVisual['BUTTON']['SHOW'] = 'N';

if (defined('EDITOR'))
    $arResult['VISUAL']['LAZYLOAD']['USE'] = false;

if ($arResult['VISUAL']['LAZYLOAD']['USE'])
    $arResult['VISUAL']['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if ($arVisual['BACKGROUND']['SHOW'] && !empty($arParams['PROPERTY_BACKGROUND'])) {
    $arProperty = ArrayHelper::getValue($arResult['ITEM'], [
        'PROPERTIES',
        $arParams['PROPERTY_BACKGROUND'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        $arFiles = Arrays::fromDBResult(CFile::GetList([], [
            'ID' => $arProperty
        ]))->each(function ($iIndex, &$arFile) {
            $arFile['SRC'] = CFile::GetFileSRC($arFile);
        })->indexBy('ID');

        $sBackground = CFile::ResizeImageGet(
            $arFiles[$arProperty],
            [
                'width' => 500,
                'height' => 500
            ],
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true
        );

        if (!empty($sBackground))
            $arResult['BACKGROUND'] = $sBackground;
    }

    if (empty($arResult['BACKGROUND']['src']))
        $arVisual['BACKGROUND']['SHOW'] = 'N';
}

$arResult['VISUAL'] = ArrayHelper::merge($arVisual, $arResult['VISUAL']);

unset($arVisual);
