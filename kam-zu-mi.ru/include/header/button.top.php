<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @global CMain $APPLICATION
 */

?>
<?php $APPLICATION->IncludeComponent(
    "intec.universe:widget",
    "buttontop",
    array(
        "RADIUS" => "10",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "0"
    ),
    false
) ?>
