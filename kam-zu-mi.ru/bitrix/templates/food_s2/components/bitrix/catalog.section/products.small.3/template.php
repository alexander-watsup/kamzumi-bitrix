<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-products-small-3'
    ]
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="catalog-section-items intec-grid intec-grid-wrap intec-grid-a-v-stretch">
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $sPicture = $arItem['PICTURE']['VALUE'];

                    if (!empty($sPicture)) {
                        $sPicture = CFile::ResizeImageGet($sPicture, [
                            'width' => 56,
                            'height' => 56
                        ], BX_RESIZE_IMAGE_PROPORTIONAL);

                        if (!empty($sPicture))
                            $sPicture = $sPicture['src'];
                    }

                    if (empty($sPicture))
                        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    $arPrice = null;

                    if (!empty($arItem['ITEM_PRICES']))
                        $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

                ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-section-item-container',
                            'intec-grid-item' => [
                                '1',
                                '1024-3',
                                '768-2',
                                '500-1'
                            ]
                        ]
                    ]) ?>
                        <div class="catalog-section-item" id="<?= $sAreaId ?>">
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'intec-grid' => [
                                        '',
                                        'nowrap',
                                        'i-h-8',
                                        'a-v-center'
                                    ]
                                ]
                            ]) ?>
                                <?php if ($arItem['PICTURE']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?= Html::beginTag('a', [
                                            'class' => [
                                                'catalog-section-item-picture',
                                                'intec-image-effect'
                                            ],
                                            'href' => $arItem['DETAIL_PAGE_URL']
                                        ]) ?>
                                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                                'alt' => $arItem['NAME'],
                                                'title' => $arItem['NAME'],
                                                'loading' => 'lazy',
                                                'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ]) ?>
                                            <div class="intec-aligner"></div>
                                        <?= Html::endTag('a') ?>
                                    </div>
                                <?php } ?>
                                <div class="intec-grid-item">
                                    <div class="catalog-section-item-name">
                                        <?= Html::tag('a', $arItem['NAME'], [
                                            'class' => 'intec-cl-text-hover',
                                            'title' => $arItem['NAME'],
                                            'href' => $arItem['DETAIL_PAGE_URL'],
                                            'target' => '_blank'
                                        ]) ?>
                                    </div>
                                    <?php if ($arVisual['PRICE']['SHOW'] && !empty($arPrice)) { ?>
                                        <div class="catalog-section-item-price">
                                            <div class="catalog-section-item-price-current">
                                                <?php if (!empty($arItem['OFFERS'])) { ?>
                                                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_3_TEMPLATE_PRICE_FROM', [
                                                        '#PRICE#' => $arPrice['PRINT_PRICE'],
                                                        '#MEASURE#' => !empty($arItem['CATALOG_MEASURE_NAME']) ? ' / '.$arItem['CATALOG_MEASURE_NAME'] : null
                                                    ]) ?>
                                                <?php } else { ?>
                                                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_3_TEMPLATE_PRICE', [
                                                        '#PRICE#' => $arPrice['PRINT_PRICE'],
                                                        '#MEASURE#' => !empty($arItem['CATALOG_MEASURE_NAME']) ? ' / '.$arItem['CATALOG_MEASURE_NAME'] : null
                                                    ]) ?>
                                                <?php } ?>
                                            </div>
                                            <?php if ($arVisual['DISCOUNT']['SHOW'] && $arPrice['DISCOUNT'] > 0) { ?>
                                                <div class="catalog-section-item-price-discount">
                                                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_3_TEMPLATE_PRICE', [
                                                        '#PRICE#' => $arPrice['PRINT_BASE_PRICE'],
                                                        '#MEASURE#' => null
                                                    ]) ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>