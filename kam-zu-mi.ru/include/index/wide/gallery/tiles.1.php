<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\collections\Arrays;
use intec\core\helpers\Html;
use intec\core\io\Path;

/**
 * @var Arrays $blocks
 * @var array $block
 * @var array $data
 * @var string $page
 * @var Path $path
 * @global CMain $APPLICATION
 */

?>
<?= Html::beginTag('div', ['style' => [
    'margin-top' => '50px',
    'margin-bottom' => '50px',
]]) ?>
    <?php $APPLICATION->IncludeComponent(
        "intec.universe:main.gallery",
        "template.2",
        array(
            "IBLOCK_TYPE" => "content",
            "IBLOCK_ID" => "36",
            "ELEMENTS_COUNT" => "8",
            "SETTINGS_USE" => "Y",
            "LAZYLOAD_USE" => "N",
            "HEADER_SHOW" => "Y",
            "HEADER_POSITION" => "center",
            "HEADER_TEXT" => "Фотогалерея",
            "DESCRIPTION_SHOW" => "N",
            "LINE_COUNT" => 4,
            "ALIGNMENT" => "center",
            "DELIMITERS" => "N",
            "WIDE" => "N",
            "FOOTER_SHOW" => "N",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => 3600000,
            "SORT_BY" => "SORT",
            "ORDER_BY" => "ASC"
        ),
        false
    ); ?>
<?= Html::endTag('div') ?>