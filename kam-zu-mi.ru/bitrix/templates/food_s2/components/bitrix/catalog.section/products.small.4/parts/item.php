<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 * @var bool $bSlider
 */

?>
<?php return function (&$arItem) use (&$sTemplateId, &$arVisual, $bSlider) {

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
    <div class="catalog-section-item" id="<?= $sAreaId ?>">
        <?= Html::beginTag('div', [
            'class' => [
                'intec-grid' => [
                    '',
                    'nowrap',
                    'i-h-6',
                    'a-v-start'
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
                        'alt' => $arItem['NAME'],
                        'title' => $arItem['NAME'],
                        'href' => $arItem['DETAIL_PAGE_URL']
                    ]) ?>
                </div>
                <?php if ($arVisual['PRICE']['SHOW'] && !empty($arPrice)) { ?>
                    <div class="catalog-section-item-price">
                        <div class="catalog-section-item-price-current">
                            <span>
                                <?php if (!empty($arItem['OFFERS'])) { ?>
                                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_4_TEMPLATE_PRICE_FROM', [
                                        '#PRICE#' => $arPrice['PRINT_PRICE'],
                                        '#MEASURE#' => !empty($arItem['CATALOG_MEASURE_NAME']) ? ' / '.$arItem['CATALOG_MEASURE_NAME'] : null
                                    ]) ?>
                                <?php } else { ?>
                                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_4_TEMPLATE_PRICE', [
                                        '#PRICE#' => $arPrice['PRINT_PRICE'],
                                        '#MEASURE#' => !empty($arItem['CATALOG_MEASURE_NAME']) ? ' / '.$arItem['CATALOG_MEASURE_NAME'] : null
                                    ]) ?>
                                <?php } ?>
                            </span>
                        </div>
                        <?php if ($arVisual['DISCOUNT']['SHOW'] && $arPrice['DISCOUNT'] > 0) { ?>
                            <div class="catalog-section-item-price-discount">
                                <span>
                                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_4_TEMPLATE_PRICE', [
                                        '#PRICE#' => $arPrice['PRINT_BASE_PRICE'],
                                        '#MEASURE#' => null
                                    ]) ?>
                                </span>
                            </div>
                        <?php } else if ($arVisual['DISCOUNT']['GLOBAL'] && $bSlider) { ?>
                            <div class="catalog-section-item-price-discount"></div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
    </div>
<?php } ?>