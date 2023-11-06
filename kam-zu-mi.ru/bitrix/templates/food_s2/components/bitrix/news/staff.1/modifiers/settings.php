<?php if (defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\template\Properties;

/**
 * @var array $arParams
 */

if (!defined('EDITOR')) {
    $arSettings = [
        'LIST' => [
            'TEMPLATE' => Properties::get('sections-staff-template')
        ]
    ];

    switch ($arSettings['LIST']['TEMPLATE']) {
        case 'blocks.1': {
            $arParams['LIST_TEMPLATE'] = 'blocks.1';
            break;
        }
        case 'blocks.2': {
            $arParams['LIST_TEMPLATE'] = 'list.2';
            break;
        }
    }

    if (Properties::get('template-images-lazyload-use')) {
        $arParams['LIST_LAZYLOAD_USE'] = 'Y';
        $arParams['DETAIL_LAZYLOAD_USE'] = 'Y';
    }
}