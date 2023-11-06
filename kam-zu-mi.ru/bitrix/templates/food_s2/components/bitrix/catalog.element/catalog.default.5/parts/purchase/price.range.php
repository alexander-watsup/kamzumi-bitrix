<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var bool $bOffers
 */

?>
<?php $vPriceRange = function (&$arItem, $bOffer = false) use (&$arVisual) { ?>
    <?php if (!empty($arItem['OFFERS']) || count($arItem['ITEM_PRICES']) <= 1) return ?>
    <?= Html::beginTag('div', [
        'class' => [
            'catalog-element-purchase-block',
            'catalog-element-price-range-container'
        ],
        'data-offer' => $bOffer ? $arItem['ID'] : 'false'
    ]) ?>
        <div class="catalog-element-price-range">
            <?php foreach ($arItem['ITEM_PRICES'] as $arPrice) { ?>
                <div class="catalog-element-price-range-item">
                    <div class="intec-grid intec-grid-a-v-end intec-grid-i-2 intec-grid-350-wrap">
                        <div class="intec-grid-item-auto intec-grid-item-350-1">
                            <div class="catalog-element-price-range-item-quantity">
                                <?php if (!empty($arPrice['QUANTITY_FROM'])) { ?>
                                    <?= Html::tag('span', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRICE_RANGE_FROM', [
                                        '#FROM#' => $arPrice['QUANTITY_FROM']
                                    ])) ?>
                                <?php } ?>
                                <?php if (!empty($arPrice['QUANTITY_TO'])) { ?>
                                    <?= Html::tag('span', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRICE_RANGE_TO', [
                                        '#TO#' => $arPrice['QUANTITY_TO']
                                    ])) ?>
                                <?php } ?>
                                <?php if (!empty($arItem['CATALOG_MEASURE_NAME'])) { ?>
                                    <?= Html::tag('span', $arItem['CATALOG_MEASURE_NAME']) ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="catalog-element-price-range-item-delimiter-container intec-grid-item">
                            <div class="catalog-element-price-range-item-delimiter"></div>
                        </div>
                        <div class="intec-grid-item-auto intec-grid-item-350-1">
                            <div class="catalog-element-price-range-item-price">
                                <?= Html::tag('span', $arPrice['PRINT_PRICE']) ?>
                                <?php if (!empty($arItem['CATALOG_MEASURE_NAME'])) { ?>
                                    <?= Html::tag('span', '/') ?>
                                    <?= Html::tag('span', $arItem['CATALOG_MEASURE_NAME']) ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php $vPriceRange($arResult);

if ($bOffers) {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $vPriceRange($arOffer, true);

    unset($arOffer);
}

unset($vPriceRange); ?>