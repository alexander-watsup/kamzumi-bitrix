<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ABOUT_TEMPLATE_1_PRESET_BLOCK_1'),
        'group' => 'about',
        'sort' => 101,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SECTION' => '',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'ELEMENTS_MODE' => 'code',
            'TITLE_SHOW' => 'Y',
            'PICTURE_SOURCES' => [
                0 => 'preview'
            ],
            'PICTURE_SIZE' => 'contain',
            'POSITION_HORIZONTAL' => 'center',
            'POSITION_VERTICAL' => 'bottom',
            'PROPERTY_LINK' => 'LINK',
            'BACKGROUND_SHOW' => 'Y',
            'PROPERTY_BACKGROUND' => 'BACKGROUND_IMAGE',
            'PROPERTY_TITLE' => 'HEADER',
            'PROPERTY_VIDEO' => 'VIDEO_LINK',
            'BUTTON_SHOW' => 'Y',
            'BUTTON_TEXT' => Loc::getMessage('PRESETS_ABOUT_TEMPLATE_1_BUTTON_TEXT')
        ]
    ]
];