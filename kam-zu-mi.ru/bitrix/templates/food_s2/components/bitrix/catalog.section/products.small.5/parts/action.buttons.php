<?php if (defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$sIconDelay = FileHelper::getFileData(__DIR__.'/../svg/delay.svg');
$sIconCompare = FileHelper::getFileData(__DIR__.'/../svg/compare.svg');

$vButtons = function (&$arItem) use (&$arResult, &$arVisual, &$sIconDelay, &$sIconCompare) {
    $arParentValues = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID'],
        'DELAY' => $arItem['DELAY']['USE']
    ];
    $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arParentValues, &$sIconDelay, &$sIconCompare) { ?>
        <div class="catalog-section-item-action-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
            <?php if ($arParentValues['DELAY'] && $arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-delay',
                        'intec-cl-background-hover',
                        'intec-cl-border-hover'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'delay',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_DELAY_ADD')
                ]) ?>
                    <?= $sIconDelay ?>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-delayed',
                        'intec-cl-background',
                        'intec-cl-border',
                        'intec-cl-background-light-hover',
                        'intec-cl-border-light-hover'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'remove',
                        'basket-state' => 'none'
                    ],
                'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_DELAY_REMOVE')
                ]) ?>
                    <?= $sIconDelay ?>
                <?= Html::endTag('div') ?>
            <?php } else if ($arResult['DELAY']['SHOW_INACTIVE']) { ?>
                <div class="catalog-section-item-action-button catalog-section-item-action-button-delay inactive">
                    <?= $sIconDelay ?>
                </div>
            <?php } ?>
            <?php if ($arResult['COMPARE']['USE']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-compare',
                        'intec-cl-background-hover',
                        'intec-cl-border-hover'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'add',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_COMPARE_ADD')
                ]) ?>
                    <?= $sIconCompare ?>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-compared',
                        'intec-cl-background',
                        'intec-cl-border',
                        'intec-cl-background-light-hover',
                        'intec-cl-border-light-hover'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'remove',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_COMPARE_REMOVE')
                ]) ?>
                    <?= $sIconCompare ?>
                <?= Html::endTag('div') ?>
            <?php } else if ($arResult['COMPARE']['SHOW_INACTIVE']) { ?>
                <div class="catalog-section-item-action-button catalog-section-item-action-button-compare inactive">
                    <?= $sIconCompare ?>
                </div>
            <?php } ?>
        </div>
    <?php };

    $fRender($arItem);

    if ($arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS']))
        foreach ($arItem['OFFERS'] as &$arOffer)
            $fRender($arOffer, true);
};