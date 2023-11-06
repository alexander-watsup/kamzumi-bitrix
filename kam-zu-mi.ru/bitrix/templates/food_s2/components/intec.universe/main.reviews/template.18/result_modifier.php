<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
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
    'VIDEO_IBLOCK_TYPE' => null,
    'VIDEO_IBLOCK_ID' => null,
    'PROPERTY_VIDEO' => null,
    'VIDEO_IBLOCK_PROPERTY_LINK' => null,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'VIDEO_SHOW' => 'N',
    'VIDEO_IMAGE_QUALITY' => 'hqdefault',
    'PROPERTY_RATING' => null,
    'RATING_SHOW' => 'N',
    'RATING_MAX' => '5',
    'BUTTON_ALL_SHOW' => 'N',
    'SLIDER_USE' => 'N',
    'SLIDER_DOTS' => 'N',
    'SLIDER_LOOP' => 'N',
    'SLIDER_AUTO_USE' => 'N',
    'SLIDER_AUTO_TIME' => 10000,
    'SLIDER_AUTO_HOVER' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arMacros = [
    'SITE_DIR' => SITE_DIR
];

$arVideos = [];

$hGetProperty = function (&$arItem, $property) {
    $property = ArrayHelper::getValue($arItem, [
        'PROPERTIES',
        $property,
        'VALUE'
    ]);

    if (!empty($property)) {
        if (Type::isArray($property))
            $property = ArrayHelper::getFirstValue($property);

        return $property;
    }

    return null;
};

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [];

    if (!empty($arParams['PROPERTY_RATING'])) {
        $arProperty = $hGetProperty($arItem, $arParams['PROPERTY_RATING']);

        if (!empty($arProperty))
            $arItem['DATA']['RATING'] = $arProperty;
    }

    if (!empty($arParams['VIDEO_IBLOCK_ID']) &&
        !empty($arParams['PROPERTY_VIDEO']) &&
        !empty($arParams['VIDEO_IBLOCK_PROPERTY_LINK'])
    ) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_VIDEO'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arVideos[] = $arProperty;
        }
    }
}

unset($arItem);

if (!empty($arVideos)) {
    $youtube = function ($url) {
        $arrUrl = parse_url($url);

        if (isset($arrUrl['query'])) {
            $arrUrlGet = explode('&', $arrUrl['query']);
            foreach ($arrUrlGet as $value) {
                $arrGetParam = explode('=', $value);
                if (!strcmp(array_shift($arrGetParam), 'v')) {
                    $videoID = array_pop($arrGetParam);
                    break;
                }
            }
            if (empty($videoID)) {
                $videoID = array_pop(explode('/', $arrUrl['path']));
            }
        } else {
            $videoID = array_pop(explode('/', $url));
        }

        return [
            'iframe' => 'https://www.youtube.com/embed/'.$videoID,
            'src' => 'https://www.youtube.com/watch?v='.$videoID,
            'mqdefault' => 'https://img.youtube.com/vi/'.$videoID.'/mqdefault.jpg',
            'hqdefault' => 'https://img.youtube.com/vi/'.$videoID.'/hqdefault.jpg',
            'sddefault' => 'https://img.youtube.com/vi/'.$videoID.'/sddefault.jpg',
            'maxresdefault' => 'https://img.youtube.com/vi/'.$videoID.'/maxresdefault.jpg',
            'id' => $videoID
        ];
    };

    $arVideosData = [];
    $rsVideos = CIBlockElement::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arParams['VIDEO_IBLOCK_ID'],
        'ID' => $arVideos
    ]);

    while ($arVideo = $rsVideos->GetNextElement()) {
        $arData = $arVideo->GetFields();
        $arData['PROPERTIES'] = $arVideo->GetProperties();

        $arVideosData[$arData['ID']] = $arData['PROPERTIES'][$arParams['VIDEO_IBLOCK_PROPERTY_LINK']]['VALUE'];

        if (Type::isArray($arVideosData[$arData['ID']]))
            $arVideosData[$arData['ID']] = ArrayHelper::getFirstValue($arVideosData[$arData['ID']]);
    }

    unset($rsVideos, $arVideo, $arData);

    if (!empty($arVideosData)) {
        foreach ($arVideosData as &$arItem) {
            $arItem = $youtube($arItem);
        }

        unset($arItem);

        $arVideosData = Arrays::from($arVideosData);

        foreach ($arResult['ITEMS'] as &$arItem) {
            $arProperty = ArrayHelper::getValue($arItem, [
                'PROPERTIES',
                $arParams['PROPERTY_VIDEO'],
                'VALUE'
            ]);

            if (!empty($arProperty)) {
                if (Type::isArray($arProperty))
                    $arProperty = ArrayHelper::getFirstValue($arProperty);

                if ($arVideosData->exists($arProperty))
                    $arItem['DATA']['VIDEO'] = $arVideosData->get($arProperty);
            }
        }

        unset($arItem);
    }

    unset($youtube, $arVideosData);
}

unset($arProperty);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'VIDEO' => [
        'SHOW' => $arParams['VIDEO_SHOW'] === 'Y',
        'QUALITY' => ArrayHelper::fromRange([
            'mqdefault',
            'hqdefault',
            'sddefault',
            'maxresdefault'
        ], $arParams['VIDEO_IMAGE_QUALITY'])
    ],
    'RATING' => [
        'SHOW' => $arParams['RATING_SHOW'] === 'Y' && !empty($arParams['PROPERTY_RATING']),
        'MAX' => intval($arParams['RATING_MAX'])
    ],
    'BUTTON_SHOW_ALL' => [
        'SHOW' => $arParams['BUTTON_ALL_SHOW'] === 'Y' && !empty($arParams['LIST_PAGE_URL']),
        'TEXT' => $arParams['BUTTON_ALL_TEXT'],
        'LINK' => StringHelper::replaceMacros(
            $arParams['LIST_PAGE_URL'],
            $arMacros
        )
    ],
    'SLIDER' => [
        'USE' => $arParams['SLIDER_USE'] === 'Y',
        'DOTS' => $arParams['SLIDER_DOTS'] === 'Y',
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTO_USE'] === 'Y',
            'TIME' => Type::toInteger($arParams['SLIDER_AUTO_TIME']),
            'HOVER' => $arParams['SLIDER_AUTO_HOVER']
        ]
    ]
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if ($arVisual['VIDEO']['SHOW'] && empty($arVideos))
    $arVisual['VIDEO']['SHOW'] = false;

if ($arVisual['SLIDER']['AUTO']['TIME'] < 1)
    $arVisual['SLIDER']['AUTO']['TIME'] = 10000;

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'], $arVisual);

unset($arVisual, $arVideos);

if (!empty($arParams['LIST_PAGE_URL']))
    $arButtons['SHOW_ALL']['LINK'] = StringHelper::replaceMacros(
        $arParams['LIST_PAGE_URL'],
        $arMacros
    );

unset($arMacros);