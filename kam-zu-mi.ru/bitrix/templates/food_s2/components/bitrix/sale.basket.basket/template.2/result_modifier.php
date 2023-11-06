<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'ORDER_FAST_USE' => 'N',
    'ORDER_FAST_TEMPLATE' => null,
    'DEFERRED_REFRESH' => 'N',
    'SHOW_RESTORE' => 'N',
    'COLUMNS_LIST' => [],
    'COLUMNS_LIST_MOBILE' => [],
    'TOTAL_BLOCK_DISPLAY' => ['top'],
    'TOTAL_WEIGHT_SHOW' => 'N',
    'TOTAL_VOLUME_SHOW' => 'N',
    'PRICE_VAT_SHOW_VALUE' => 'N',
    'SHOW_FILTER' => 'N',
    'USE_DYNAMIC_SCROLL' => 'N',
    'LABEL_PROP' => [],
    'LABEL_PROP_MOBILE' => [],
    'LABEL_PROP_POSITION' => [],
    'PRODUCT_BLOCKS_ORDER' => 'props,sku,columns',
    'OFFERS_PROPS' => [],
    'EMPTY_BASKET_HINT_PATH' => '#SITE_DIR#',
    'USE_ENHANCED_ECOMMERCE' => 'N',
    'DATA_LAYER_NAME' => 'dataLayer',
    'BRAND_PROPERTY' => null
], $arParams);