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
	"intec.universe:main.sections", 
	"template.1", 
	array(
		"IBLOCK_TYPE" => "catalogs",
		"IBLOCK_ID" => "25",
		"QUANTITY" => "N",
		"SECTIONS_MODE" => "id",
		"DEPTH" => "1",
		"ELEMENTS_COUNT" => "5",
		"SETTINGS_USE" => "Y",
		"LAZYLOAD_USE" => "N",
		"HEADER_SHOW" => "Y",
		"HEADER_POSITION" => "center",
		"HEADER_TEXT" => "Популярные категории",
		"DESCRIPTION_SHOW" => "N",
		"LINE_COUNT" => "5",
		"SECTION_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"SORT_BY" => "SORT",
		"ORDER_BY" => "ASC",
		"COMPONENT_TEMPLATE" => "template.1",
		"SECTIONS" => array(
			0 => "1865",
			1 => "1866",
			2 => "1867",
			3 => "1868",
			4 => "1845",
			5 => "1825",
			6 => "",
		),
		"LIST_PAGE_URL" => ""
	),
	false
); ?>
<?= Html::endTag('div') ?>