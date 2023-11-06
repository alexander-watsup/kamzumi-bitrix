<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
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
    'PROPERTY_ADVANTAGES' => null,
    'POSITION_VERTICAL' => 'center',
    'POSITION_HORIZONTAL' => 'center',
    'SVG_FILE_USE' => 'Y',

    'VIEW' => 1,
    'BUTTON_VIEW' => 1,
    'ADVANTAGES_COLUMNS' => 2,
    'ADVANTAGES_IBLOCK_ID' => null,
    'ADVANTAGES_PROPERTY_SVG_FILE' => null,
    'ADVANTAGES_SHOW' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'VIEW' => ArrayHelper::fromRange([1, 2, 3], $arParams['VIEW']),
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
    'BUTTON_MORE' => [
        'SHOW' => $arParams['BUTTON_SHOW'] === 'Y',
        'TEXT' => $arParams['BUTTON_TEXT'],
        'VIEW' => ArrayHelper::fromRange([1, 2], $arParams['BUTTON_VIEW'])
    ],
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'ADVANTAGES' => [
        'SHOW' => $arParams['ADVANTAGES_SHOW'] === 'Y',
        'COLUMNS' => ArrayHelper::fromRange([2, 3], $arParams['ADVANTAGES_COLUMNS'])
    ]
];

if (empty($arResult['LINK']))
    $arVisual['BUTTON']['SHOW'] = 'N';

if (defined('EDITOR'))
    $arResult['VISUAL']['LAZYLOAD']['USE'] = false;

if ($arResult['VISUAL']['LAZYLOAD']['USE'])
    $arResult['VISUAL']['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arResult['VISUAL'] = ArrayHelper::merge($arVisual, $arResult['VISUAL']);

unset($arVisual);

if ($arResult['ITEM']) {
    $arResult['ADVANTAGES'] = [];

    if (!empty($arParams['PROPERTY_ADVANTAGES'])) {
        $arProperty = ArrayHelper::getValue($arResult['ITEM'], [
            'PROPERTIES',
            $arParams['PROPERTY_ADVANTAGES'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {

            $arSelect = [
                'ID',
                'IBLOCK_ID',
                'NAME',
                'DATE_ACTIVE_FROM',
                'PREVIEW_PICTURE',
                'DETAIL_PICTURE',
                'PREVIEW_TEXT',
                'PROPERTY_*'
            ];
            $arFilter = Array(
                "IBLOCK_ID"=> $arParams['ADVANTAGES_IBLOCK_ID'],
                "ACTIVE_DATE"=>"Y",
                "ACTIVE"=>"Y",
                "ID" => $arProperty
            );
            $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>50), $arSelect);
            $i = 0;
            while ($ob = $res->GetNextElement()) {
                $arResult['ADVANTAGES'][$i] = $ob->GetFields();
                $arResult['ADVANTAGES'][$i]['PROPERTIES'] = $ob->GetProperties();

                $i++;
            }

            $arFilesId = [];

            foreach ($arResult['ADVANTAGES'] as $arItem) {
                $arFilesId[] = $arItem['PREVIEW_PICTURE'];
                $arFilesId[] = $arItem['DETAIL_PICTURE'];

                if ($arParams['SVG_FILE_USE'] === 'Y' && !empty($arParams['ADVANTAGES_PROPERTY_SVG_FILE'])) {
                    $arPropertySvg = null;

                    $arPropertySvg = ArrayHelper::getValue($arItem, [
                        'PROPERTIES',
                        $arParams['ADVANTAGES_PROPERTY_SVG_FILE']
                    ]);

                    if (!empty($arPropertySvg['VALUE']))
                        $arFilesId[] = $arPropertySvg['VALUE'];
                }
            }

            unset($arItem, $arPropertySvg);

            if (!empty($arFilesId)) {
                if (count($arFilesId) > 1) {
                    $arFilter = [
                        '@ID' => implode(',', $arFilesId)
                    ];
                } else {
                    $arFilter = [
                        'ID' => $arFilesId
                    ];
                }
                $arFiles = Arrays::fromDBResult(CFile::GetList([], [
                    '@ID' => implode(',', $arFilter)
                ]))->each(function ($iIndex, &$arFile) {
                    $arFile['SRC'] = CFile::GetFileSRC($arFile);
                })->indexBy('ID');
            }

            foreach ($arResult['ADVANTAGES'] as &$arItem) {
                $arItem['IMAGE'] = [];

                if (!empty($arItem['DETAIL_PICTURE']))
                    if (ArrayHelper::keyExists($arItem['DETAIL_PICTURE'], $arFiles))
                        $arItem['IMAGE'] = $arFiles[$arItem['DETAIL_PICTURE']];

                if (!empty($arItem['PREVIEW_PICTURE']))
                    if (ArrayHelper::keyExists($arItem['PREVIEW_PICTURE'], $arFiles))
                        $arItem['IMAGE'] = $arFiles[$arItem['PREVIEW_PICTURE']];

                if ($arParams['SVG_FILE_USE'] === 'Y' && !empty($arParams['ADVANTAGES_PROPERTY_SVG_FILE'])) {
                    $arPropertySvg = ArrayHelper::getValue($arItem, [
                        'PROPERTIES',
                        $arParams['ADVANTAGES_PROPERTY_SVG_FILE']
                    ]);

                    if (!empty($arPropertySvg['VALUE']))
                        if (ArrayHelper::keyExists($arPropertySvg['VALUE'], $arFiles))
                            $arItem['IMAGE'] = $arFiles[$arPropertySvg['VALUE']];
                }
            }

            unset($arItem, $arFiles, $arFilesId);
        }
    }
}