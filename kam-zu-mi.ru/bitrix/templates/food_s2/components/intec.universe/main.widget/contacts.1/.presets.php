<?php

use Bitrix\Main\Localization\Loc;

return [
    [
        'name' => Loc::getMessage('PRESETS_CONTACTS_1_PRESET_LIST_1'),
        'group' => 'contacts',
        'sort' => 101,
        'properties' => [
            'SETTINGS_USE' => 'Y',
            'NEWS_COUNT' => '20',
            'PROPERTY_CODE' => [
                'ADDRESS',
                'CITY',
                'PHONE',
                'MAP'
            ],
            'PROPERTY_ADDRESS' => 'ADDRESS',
            'PROPERTY_CITY' => 'CITY',
            'PROPERTY_PHONE' => 'PHONE',
            'PROPERTY_MAP' => 'MAP',
            'MAP_VENDOR' => 'yandex',
            'WEB_FORM_TEMPLATE' => '.default',
            'WEB_FORM_NAME' => Loc::getMessage('PRESETS_CONTACTS_1_WEB_FORM_NAME'),
            'WEB_FORM_CONSENT_URL' => '#SITE_DIR#company/consent/',
            'FEEDBACK_BUTTON_TEXT' => Loc::getMessage('PRESETS_CONTACTS_1_FEEDBACK_BUTTON_TEXT'),
            'FEEDBACK_TEXT' => Loc::getMessage('PRESETS_CONTACTS_1_FEEDBACK_TEXT'),
            'FEEDBACK_IMAGE' => '#TEMPLATE_PATH#/images/face.png',
            'ADDRESS_SHOW' => 'Y',
            'PHONE_SHOW' => 'Y',
            'SHOW_FORM' => 'Y',
            'FEEDBACK_TEXT_SHOW' => 'Y',
            'FEEDBACK_IMAGE_SHOW' => 'Y',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => 3600000
        ]
    ]
];