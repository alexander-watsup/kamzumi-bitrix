<?php if (defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arSection
 * @var array $arSort
 * @var array $arViews
 * @var array $arFilter
 * @var array $arElements
 * @var bool $bIsAjax
 */

?>
<!--noindex-->
<div class="catalog-panel intec-grid intec-grid-nowrap intec-grid-i-h-10 intec-grid-a-v-center" data-role="catalog.panel">
    <?php if (!$bIsAjax) { ?>
        <?php if ($arFilter['SHOW'] || $searchFilterShow) { ?>
            <div class="catalog-panel-filter intec-grid-item-auto">
                <div class="catalog-panel-filter-button intec-cl-background" data-role="catalog.filter.button">
                    <i class="far fa-filter"></i>
                </div>
            </div>
        <?php } ?>
        <div class="catalog-panel-sorting intec-grid-item-auto" data-order="<?= $arSort['ORDER'] ?>">
            <div class="catalog-panel-sorting-wrapper intec-grid intec-grid-nowrap intec-grid-i-h-10 intec-grid-a-v-center">
                <?php foreach ($arSort['PROPERTIES'] as $sSortProperty => $arSortProperty) { ?>
                <?php
                    $sSortOrder = $arSort['ORDER'];

                    if ($arSortProperty['ACTIVE'])
                        $sSortOrder = $arSort['ORDER'] === 'asc' ? 'desc' : 'asc';
                ?>
                    <?= Html::beginTag('a', [
                        'href' => $APPLICATION->GetCurPageParam('sort='.$arSortProperty['VALUE'].'&order='.$sSortOrder, ['sort', 'order']),
                        'class' => [
                            'catalog-panel-sort',
                            'intec-grid-item-auto',
                            $arSortProperty['ACTIVE'] ? 'intec-cl-text' : 'intec-cl-text-hover'
                        ],
                        'data' => [
                            'active' => $arSortProperty['ACTIVE'] ? 'true' : 'false'
                        ]
                    ]) ?>
                        <i class="catalog-panel-sort-icon <?= $arSortProperty['ICON'] ?>"></i>
                        <div class="catalog-panel-sort-text">
                            <?= $arSortProperty['NAME'] ?>
                        </div>
                        <?php if ($arSortProperty['VALUE'] === 'price') { ?>
                            <span class="catalog-panel-sort-text" <?= !isset($_GET['sort']) ? 'style="display:none;"' : ''?>>:</span>
                            <div class="catalog-panel-sort-order catalog-panel-sort-order-price" <?= !isset($_GET['sort']) ? 'style="display:none;"' : ''?>>
                                <span class="catalog-panel-sort-order-icon catalog-panel-sort-order-icon-asc">
                                    <span><?= Loc::getMessage('C_CATALOG_CATALOG_1_SORTING_PRICE_ASC') ?></span>
                                    <i class="far fa-angle-down"></i>
                                </span>
                                <span class="catalog-panel-sort-order-icon catalog-panel-sort-order-icon-desc">
                                    <span><?= Loc::getMessage('C_CATALOG_CATALOG_1_SORTING_PRICE_DESC') ?></span>
                                    <i class="far fa-angle-up"></i>
                                </span>
                            </div>
                        <?php } else { ?>
                            <div class="catalog-panel-sort-order">
                                <i class="catalog-panel-sort-order-icon catalog-panel-sort-order-icon-asc far fa-angle-up"></i>
                                <i class="catalog-panel-sort-order-icon catalog-panel-sort-order-icon-desc far fa-angle-down"></i>
                            </div>
                        <?php } ?>
                    <?= Html::endTag('a') ?>
                <?php } ?>
                <?php
                    unset($sSortOrder);
                    unset($arSortProperty);
                    unset($sSortProperty);
                ?>
            </div>
        </div>
        <div class="intec-grid-item"></div>
        <div class="catalog-panel-views intec-grid-item-auto">
            <div class="catalog-panel-views-wrapper intec-grid intec-grid-nowrap intec-grid-i-h-10 intec-grid-a-v-center">
                <?php foreach($arViews as $sView => $arView) { ?>
                    <?= Html::beginTag('a', [
                        'href' => $APPLICATION->GetCurPageParam('view='.$arView['VALUE'], ['view']),
                        'class' => [
                            'catalog-panel-view',
                            'intec-grid-item-auto'
                        ],
                        'data' => [
                            'active' => $arView['ACTIVE'] ? 'true' : 'false'
                        ]
                    ]) ?>
                        <i class="<?= $arView['ICON'] ?>"></i>
                    <?= Html::endTag('a') ?>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
<!--/noindex-->
<?php if ($arFilter['SHOW']) { ?>
<?php
    $sTemplate = $arFilter['TEMPLATE'];

    if (StringHelper::startsWith($sTemplate, 'horizontal'))
        if ($sTemplate === 'horizontal.2') {
            $sTemplate = 'vertical.2';
        } else {
            $sTemplate = 'vertical.1';
        }
?>
    <div class="catalog-filter-mobile" data-role="catalog.filter">
        <?php if (!$bIsAjax) { ?>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:catalog.smart.filter',
                $sTemplate,
                ArrayHelper::merge($arFilter['PARAMETERS'], [
                    'MOBILE' => 'Y'
                ]),
                $component
            ) ?>
        <?php } ?>
    </div>
<?php } ?>