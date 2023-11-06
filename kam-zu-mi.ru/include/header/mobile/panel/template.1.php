<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @global CMain $APPLICATION
 */

?>
<?php $APPLICATION->IncludeComponent(
    'intec.universe:sale.basket.small',
    'panel.1',
    array(
        "COMPARE_SHOW" => "Y",
        "COMPARE_CODE" => "compare",
        "COMPARE_IBLOCK_TYPE" => "catalogs",
        "COMPARE_IBLOCK_ID" => "25",
        "SETTINGS_USE" => "Y",
        "FORM_ID" => "1",
        "BASKET_SHOW" => "Y",
        "FORM_SHOW" => "Y",
        "PERSONAL_SHOW" => "Y",
        "FORM_TITLE" => "Заказать звонок",
        "DELAYED_SHOW" => "Y",
        "CATALOG_URL" => "/catalog/",
        "BASKET_URL" => "/personal/basket/",
        "ORDER_URL" => "/personal/basket/order.php",
        "COMPARE_URL" => "/catalog/compare.php",
        "PERSONAL_URL" => "/personal/profile/",
        "CONSENT_URL" => "/company/consent/",
        "REGISTER_URL" => "/personal/profile/",
        "FORGOT_PASSWORD_URL" => "/personal/profile/?forgot_password=yes",
        "PROFILE_URL" => "/personal/profile/"
    ),
    false,
    array()
); ?>