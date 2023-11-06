<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;
use intec\core\collections\Arrays;
use intec\core\io\Path;

/**
 * @var Arrays $blocks
 * @var string $page
 * @var Closure $render($block, $data = [])
 * @var Path $path
 * @global CMain $APPLICATION
 */

?>
<div class="intec-template-page-wrapper intec-content intec-content-visible">
    <div class="intec-template-page-wrapper-2 intec-content-wrapper">
        <div class="intec-content-left">
            <?php $APPLICATION->IncludeComponent(
                "bitrix:menu",
                "vertical.1",
                array(
                    "COMPONENT_TEMPLATE" => "vertical",
                    "ROOT_MENU_TYPE" => "catalog",
                    "IBLOCK_TYPE" => "catalogs",
                    "IBLOCK_ID" => "25",
                    "PROPERTY_IMAGE" => "UF_IMAGE",
                    "MENU_CACHE_TYPE" => "N",
                    "MENU_CACHE_TIME" => "3600",
                    "MENU_CACHE_USE_GROUPS" => "N",
                    "MENU_CACHE_GET_VARS" => array(
                    ),
                    "MAX_LEVEL" => "4",
                    "CHILD_MENU_TYPE" => "catalog",
                    "USE_EXT" => "Y",
                    "DELAY" => "N",
                    "ALLOW_MULTI_SELECT" => "N"
                ),
                false
            ); ?>
        </div>
        <div class="intec-content-right">
            <div class="intec-template-page-content intec-content-right-wrapper">
            <?php
                $render($blocks->get('banner'));
                $render($blocks->get('icons'));
                $render($blocks->get('products'));
                $render($blocks->get('categories'));
                $render($blocks->get('stages'));
                $render($blocks->get('sections'));
                $render($blocks->get('gallery'));
                $render($blocks->get('advantages'));
                $render($blocks->get('video'));
                $render($blocks->get('certificates'));
                $render($blocks->get('faq'));
                $render($blocks->get('videos'));
                $render($blocks->get('shares'));
                $render($blocks->get('about'));
                $render($blocks->get('reviews'));
            ?>
            </div>
        </div>
    </div>
</div>
<?php $render($blocks->get('contacts')) ?>
