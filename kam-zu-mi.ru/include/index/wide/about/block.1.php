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
    'margin-bottom' => '50px'
]]) ?>
<?php $APPLICATION->IncludeComponent(
	"intec.universe:main.about", 
	"template.1", 
	array(
        "IBLOCK_TYPE" => "content",
        "IBLOCK_ID" => "17",
		"SECTIONS_MODE" => "id",
		"SECTION" => "",
        "SETTINGS_USE" => "Y",
        "LAZYLOAD_USE" => "N",
        "SORT_BY" => "SORT",
        "ORDER_BY" => "ASC",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => 3600000,
        "ELEMENTS_MODE" => "code",
        "ELEMENT" => "about_1",
        "TITLE_SHOW" => "Y",
		"PICTURE_SOURCES" => array(
			0 => "preview",
		),
        "PICTURE_SIZE" => "contain",
        "POSITION_HORIZONTAL" => "center",
        "POSITION_VERTICAL" => "bottom",
		"PROPERTY_LINK" => "LINK",
        "BACKGROUND_SHOW" => "Y",
        "PROPERTY_BACKGROUND" => "BACKGROUND_IMAGE",
		"PROPERTY_TITLE" => "HEADER",
		"PROPERTY_VIDEO" => "VIDEO_LINK",
		"BUTTON_SHOW" => "Y",
		"BUTTON_TEXT" => "Узнать подробнее"
	),
	false
); ?>
<?= Html::endTag('div') ?>