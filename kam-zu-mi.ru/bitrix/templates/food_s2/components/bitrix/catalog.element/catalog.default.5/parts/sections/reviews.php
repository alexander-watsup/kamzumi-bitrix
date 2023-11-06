<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var string $sTemplateId
 */

$sPrefix = 'REVIEWS_';

$iLength = StringHelper::length($sPrefix);

$arProperties = [];
$arExcluded = [
    'SHOW',
    'NAME'
];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, $iLength);

    if (ArrayHelper::isIn($sKey, $arExcluded))
        continue;

    $arProperties[$sKey] = $sValue;
}

unset($sPrefix, $iLength, $arExcluded, $sKey, $sValue);

$arProperties = ArrayHelper::merge([
    'ELEMENT_ID' => $arResult['ID'],
    'DISPLAY_REVIEWS_COUNT' => 5,
    'AJAX_MODE' => 'Y',
    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-reviews',
    'AJAX_OPTION_SHADOW' => 'N',
    'AJAX_OPTION_JUMP' => 'N',
    'AJAX_OPTION_STYLE' => 'Y',
    'ITEM_NAME' => $arResult['NAME']
], $arProperties);

?>
<div data-role="reviews"></div>
<script type="text/javascript">
    (function ($, api) {
        let root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);

        universe.components.get(<?= JavaScript::toObject([
            'component' => 'intec.universe:reviews',
            'template' => 'template.2',
            'parameters' => $arProperties
        ]) ?>, function (content) {
            $('[data-role="reviews"]', root).html(content);
        });
    })(jQuery, intec);
</script>
