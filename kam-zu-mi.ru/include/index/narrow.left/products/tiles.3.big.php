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
        "intec.universe:main.widget",
        "products.4",
        array(
            "SETTINGS_USE" => "Y",
            "LAZYLOAD_USE" => "N",
            "IBLOCK_TYPE" => "catalogs",
            "IBLOCK_ID" => "25",
            "ELEMENTS_COUNT" => 100,
            "MODE" => "all",
            "ACTION" => "buy",
            "PRICE_CODE" => Array(
                "BASE"
            ),
            "DISCOUNT_SHOW" => "Y",
            "SLIDER_USE" => "N",
            "TITLE_SHOW" => "N",
            "DESCRIPTION_SHOW" => "N",
            "COLUMNS" => 3,
            "LINES" => 2,
            "BLOCKS_HEADER_SHOW" => "Y",
            "BLOCKS_HEADER_TEXT" => "Товары",
            "BLOCKS_HEADER_ALIGN" => "left",
            "BLOCKS_DESCRIPTION_SHOW" => "N",
            "SECTION_URL" => "",
            "DETAIL_URL" => "",
            "BASKET_URL" => "/personal/basket/",
            "CONSENT_URL" => "/company/consent/",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => 3600000,
            "FORM_ID" => "3",
            "FORM_PROPERTY_PRODUCT" => "form_text_7",
            "ORDER_FAST_USE" => "N",
            "DELAY_USE" => "Y",
            "OFFERS_LIMIT" => "0",
            "BANNER_SHOW" => "Y",
            "PROPERTY_BANNER_SHOW" => "MAIN_BANNER_SHOW",
            "PROPERTY_BANNER_PICTURE" => "MAIN_BANNER_PICTURE",
            "PROPERTY_BANNER_THEME" => "MAIN_BANNER_THEME",
            "QUICK_VIEW_USE" => "Y",
            "QUICK_VIEW_DETAIL" => "N",
            "QUICK_VIEW_TEMPLATE" => 2,
            "QUICK_VIEW_LAZYLOAD_USE" => "N",
            "QUICK_VIEW_PROPERTY_CODE" => Array(
                "PROPERTY_TYPE",
                "PROPERTY_QUANTITY_OF_STRIPS",
                "PROPERTY_POWER",
                "PROPERTY_PROCREATOR",
                "PROPERTY_SCOPE",
                "PROPERTY_DISPLAY",
                "PROPERTY_WEIGTH",
                "PROPERTY_ENERGY_CONSUMPTION",
                "PROPERTY_SETTINGS",
                "PROPERTY_COMPOSITION",
                "PROPERTY_LENGTH",
                "PROPERTY_SEASON",
                "PROPERTY_PATTERN"
            ),
            "QUICK_VIEW_PROPERTY_MARKS_HIT" => "HIT",
            "QUICK_VIEW_PROPERTY_MARKS_NEW" => "NEW",
            "QUICK_VIEW_PROPERTY_MARKS_RECOMMEND" => "RECOMMEND",
            "QUICK_VIEW_PROPERTY_PICTURES" => "PICTURES",
            "QUICK_VIEW_OFFERS_PROPERTY_PICTURES" => "PICTURES",
            "QUICK_VIEW_DELAY_USE" => "Y",
            "QUICK_VIEW_MARKS_SHOW" => "Y",
            "QUICK_VIEW_MARKS_ORIENTATION" => "horizontal",
            "QUICK_VIEW_GALLERY_PREVIEW" => "Y",
            "QUICK_VIEW_QUANTITY_SHOW" => "Y",
            "QUICK_VIEW_QUANTITY_MODE" => "number",
            "QUICK_VIEW_ACTION" => "buy",
            "QUICK_VIEW_COUNTER_SHOW" => "Y",
            "QUICK_VIEW_DESCRIPTION_SHOW" => "Y",
            "QUICK_VIEW_DESCRIPTION_MODE" => "preview",
            "QUICK_VIEW_PROPERTIES_SHOW" => "Y",
            "QUICK_VIEW_DETAIL_SHOW" => "Y",
            "PROPERTY_CATEGORY" => "CATEGORY",
            "PROPERTY_ORDER_USE" => "ORDER_USE",
            "PROPERTY_MARKS_HIT" => "HIT",
            "PROPERTY_MARKS_NEW" => "NEW",
            "PROPERTY_MARKS_RECOMMEND" => "RECOMMEND",
            "PROPERTY_PICTURES" => "PICTURES",
            "PROPERTY_STORES_SHOW" => "STORES_SHOW",
            "PROPERTY_ARTICLE" => "ARTICLE",
            "OFFERS_PROPERTY_PICTURES" => "PICTURES",
            "OFFERS_PROPERTY_STORES_SHOW" => "STORES_SHOW",
            "OFFERS_PROPERTY_ARTICLE" => "ARTICLE",
            "COMPARE_PATH" => "/catalog/compare.php",
            "COMPARE_NAME" => "compare",
            "SHOW_PRICE_COUNT" => "1",
            "TABS_ALIGN" => "left",
            "BORDERS" => "Y",
            "BORDERS_STYLE" => "squared",
            "ARTICLE_SHOW" => "Y",
            "MARKS_SHOW" => "Y",
            "NAME_POSITION" => "middle",
            "NAME_ALIGN" => "left",
            "PRICE_ALIGN" => "start",
            "IMAGE_SLIDER_SHOW" => "Y",
            "IMAGE_SLIDER_NAV_SHOW" => "N",
            "IMAGE_SLIDER_OVERLAY_USE" => "Y",
            "COUNTER_SHOW" => "Y",
            "OFFERS_USE" => "Y",
            "OFFERS_ALIGN" => "left",
            "OFFERS_VIEW" => "default",
            "VOTE_SHOW" => "Y",
            "VOTE_ALIGN" => "left",
            "VOTE_MODE" => "rating",
            "QUANTITY_SHOW" => "Y",
            "QUANTITY_MODE" => "logic",
            "QUANTITY_ALIGN" => "left",
            "USE_COMPARE" => "Y",
            "CONVERT_CURRENCY" => "N",
            "USE_PRICE_COUNT" => "N",
            "PRICE_VAT_INCLUDE" => "N",
            "VIEW" => "sections",
            "SECTIONS_TITLE_SHOW" => "Y",
            "SECTIONS_TITLE_ALIGN" => "center"
        ),
        false
    ); ?>
<?= Html::endTag('div') ?>