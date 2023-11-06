<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_ABOUT_TEMPLATE_2_PRESET_BLOCK_2'),
        'group' => 'about',
        'sort' => 102,
        'properties' => [
            'SECTIONS_MODE' => 'id',
            'SECTION' => '',
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'ELEMENTS_MODE' => 'code',
            'ELEMENT' => 'about_2',
            'PICTURE_SOURCES' => [
                0 => 'preview'
            ],
            'PICTURE_SIZE' => 'contain',
            'POSITION_HORIZONTAL' => 'center',
            'POSITION_VERTICAL' => 'center',
            'TITLE_SHOW' => 'Y',
            'ADVANTAGES_SHOW' => 'Y',
            'PROPERTY_ADVANTAGES' => 'ADVANTAGES',
            'ADVANTAGES_PROPERTY_SVG_FILE' => 'ICON',
            'ADVANTAGES_COLUMNS' => '2',
            'VIEW' => '1',
            'PROPERTY_LINK' => 'LINK',
            'PROPERTY_TITLE' => 'HEADER',
            'PROPERTY_VIDEO' => 'VIDEO_LINK',
            'BUTTON_SHOW' => 'Y',
            'BUTTON_VIEW' => '1',
            'BUTTON_TEXT' => Loc::getMessage('PRESETS_ABOUT_TEMPLATE_2_BUTTON_TEXT')
        ]
    ]
];