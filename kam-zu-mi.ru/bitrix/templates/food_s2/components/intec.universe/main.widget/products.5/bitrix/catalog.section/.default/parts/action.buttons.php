<?php if (defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$vButtons = function (&$arItem) use (&$arResult, &$arVisual) {
    $arParentValues = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID'],
        'DELAY' => $arItem['DELAY']['USE']
    ];
    $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arParentValues) { ?>
        <div class="widget-item-action-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
            <?php if ($arResult['COMPARE']['USE']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-compare',
                        'intec-cl-background-hover'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'add',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_COMPARE_ADD')
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/compare.svg') ?>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-compared',
                        'intec-cl-background'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'remove',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ],
                'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_COMPARE_REMOVE')
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/compare.svg') ?>
                <?= Html::endTag('div') ?>
            <?php } else if ($arResult['COMPARE']['SHOW_INACTIVE']) { ?>
                <div class="widget-item-action-button widget-item-action-button-compare inactive">
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/compare.svg') ?>
                </div>
            <?php } ?>
            <?php if ($arParentValues['DELAY'] && $arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-delay',
                        'intec-cl-background-hover'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'delay',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_DELAY_ADD')
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-delayed',
                        'intec-cl-background'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'remove',
                        'basket-state' => 'none'
                    ],
                'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_DELAY_REMOVE')
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                <?= Html::endTag('div') ?>
            <?php } else if ($arResult['DELAY']['SHOW_INACTIVE']) { ?>
                <div class="widget-item-action-button widget-item-action-button-delay inactive">
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                </div>
            <?php } ?>
        </div>
    <?php };

    $fRender($arItem);

    if ($arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS']))
        foreach ($arItem['OFFERS'] as &$arOffer)
            $fRender($arOffer, true);
};