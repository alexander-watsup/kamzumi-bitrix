<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_REVIEWS_TEMPLATE_5_PRESET_BLOCKS_1'),
        'group' => 'reviews',
        'sort' => 101,
        'properties' => [
            'ELEMENTS_COUNT' => '4',
            'SECTIONS_MODE' => 'id',
            'SETTINGS_USE' => 'Y',
            'LAZYLOAD_USE' => 'N',
            'HEADER_SHOW' => 'Y',
            'HEADER_POSITION' => 'center',
            'HEADER_TEXT' => Loc::getMessage('PRESETS_REVIEWS_TEMPLATE_5_HEADER_TEXT'),
            'DESCRIPTION_SHOW' => 'N',
            'COLUMNS' => 4,
            'LINK_USE' => 'N',
            'FOOTER_SHOW' => 'Y',
            'FOOTER_POSITION' => 'center',
            'FOOTER_BUTTON_SHOW' => 'Y',
            'FOOTER_BUTTON_TEXT' => Loc::getMessage('PRESETS_REVIEWS_TEMPLATE_5_FOOTER_BUTTON_TEXT'),
            'LIST_PAGE_URL' => '#SITE_DIR#company/reviews/',
            'SECTION_URL' => '',
            'DETAIL_URL' => '',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC'
        ]
    ]
];