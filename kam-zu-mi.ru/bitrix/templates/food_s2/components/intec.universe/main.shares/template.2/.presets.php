<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_2_PRESET_BLOCKS_1'),
        'group' => 'shares',
        'sort' => 101,
        'properties' => [
            'ELEMENTS_COUNT' => '4',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_BLOCK_SHOW' => 'Y',
            'HEADER_BLOCK_POSITION' => 'center',
            'HEADER_BLOCK_TEXT' => Loc::getMessage('PRESETS_SHARES_TEMPLATE_2_HEADER_BLOCK_TEXT'),
            'DESCRIPTION_BLOCK_SHOW' => 'N',
            'LINE_COUNT' => 2,
            'LINK_USE' => 'Y',
            'DESCRIPTION_USE' => 'N',
            'DATE_SHOW' => 'Y',
            'DATE_FORMAT' => 'd.m.Y',
            'SEE_ALL_SHOW' => 'N',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];