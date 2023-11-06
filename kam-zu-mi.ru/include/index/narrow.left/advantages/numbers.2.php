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
    'margin-bottom' => '50px'
]]) ?>
    <?php $APPLICATION->IncludeComponent(
        "intec.universe:main.advantages",
        "template.32",
        array(
            "IBLOCK_TYPE" => "content",
            "IBLOCK_ID" => "21",
            "SECTIONS_MODE" => "id",
            "SETTINGS_USE" => "Y",
            "LAZYLOAD_USE" => "N",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => 3600000,
            "SORT_BY" => "SORT",
            "ORDER_BY" => "ASC",
            "ELEMENTS_COUNT" => 2,
            "PROPERTY_NUMBER" => "NUMBER_VALUE",
            "HEADER_SHOW" => "Y",
            "HEADER_POSITION" => "left",
            "HEADER" => "О нас в цифрах",
            "DESCRIPTION_SHOW" => "Y",
            "DESCRIPTION_POSITION" => "left",
            "DESCRIPTION" => "Предлагаем вам широкий ассортимент продукции, который в совокупности с нашими услугами поможет повысить эффективность вашего бизнеса.",
            "NUMBER_SHOW" => "Y",
            "NUMBER_ALIGN" => "center",
            "PREVIEW_SHOW" => "Y",
            "BUTTON_SHOW" => "Y",
            "BUTTON_TEXT" => "Узнать подробнее",
            "BUTTON_LINK" => "/",
            "PREVIEW_ALIGN" => "center"
        ),
        false
    ); ?>
<?= Html::endTag('div') ?>