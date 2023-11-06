<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$arNavigation = !empty($arResult['NAV_RESULT']) ? [
    'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
    'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
    'NavNum' => $arResult['NAV_RESULT']->NavNum
] : [
    'NavPageCount' => 1,
    'NavPageNomer' => 1,
    'NavNum' => $this->randString()
];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sTemplateContainer = $sTemplateId.'-'.$arNavigation['NavNum'];

$arVisual = $arResult['VISUAL'];

$arVisual['NAVIGATION']['LAZY']['BUTTON'] =
    $arVisual['NAVIGATION']['LAZY']['BUTTON'] &&
    $arNavigation['NavPageNomer'] < $arNavigation['NavPageCount'];

?>
<div class="ns-bitrix c-catalog-section c-catalog-section-services-list-6" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['NAVIGATION']['TOP']['SHOW']) { ?>
                <div class="catalog-section-navigation-top" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
                    <!-- pagination-container -->
                    <?= $arResult['NAV_STRING'] ?>
                    <!-- pagination-container -->
                </div>
            <?php } ?>
            <!-- items-container -->
            <div class="catalog-section-content intec-grid intec-grid-wrap" data-entity="<?= $sTemplateContainer ?>">
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $arData = $arItem['DATA'];

                    $sPicture = $arItem['PREVIEW_PICTURE'];

                    if (!empty($sPicture)) {
                        $sPicture = CFile::ResizeImageGet($sPicture, [
                            'width' => 212,
                            'height' => 118
                        ], BX_RESIZE_IMAGE_EXACT);

                        if (!empty($sPicture))
                            $sPicture = $sPicture['src'];
                    }

                    if (empty($sPicture))
                        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                ?>
                    <div class="intec-grid-item-1" data-entity="items-row">
                        <div class="catalog-section-item" id="<?= $sAreaId ?>">
                            <div class="intec-grid intec-grid-nowrap intec-grid-i-h-12 intec-grid-i-v-8 intec-grid-500-wrap">
                                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto intec-grid-item-500-1">
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
                                <div class="intec-grid-item intec-grid-item-500-1">
                                    <div class="catalog-section-item-name">
                                        <?= Html::tag('a', $arItem['NAME'], [
                                            'class' => 'intec-cl-text-hover',
                                            'href' => $arItem['DETAIL_PAGE_URL']
                                        ]) ?>
                                    </div>
                                    <div class="catalog-section-item-description">
                                        <?= $arItem['PREVIEW_TEXT'] ?>
                                    </div>
                                    <?php if ($arVisual['PROPERTIES']['SHOW'] && !empty($arItem['DISPLAY_PROPERTIES'])) { ?>
                                        <?php $iCount = 0 ?>
                                        <div class="catalog-section-item-properties">
                                            <?php foreach ($arItem['DISPLAY_PROPERTIES'] as $arProperty) {
                                                if ($arVisual['PROPERTIES']['COUNT'] > 0 && $iCount == $arVisual['PROPERTIES']['COUNT'])
                                                    break;

                                                if (empty($arProperty['DISPLAY_VALUE']))
                                                    continue;

                                                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                                                    $arProperty['DISPLAY_VALUE'] = implode(', ', $arProperty['DISPLAY_VALUE']);

                                            ?>
                                                <div class="catalog-section-item-properties-item">
                                                    <?= Html::tag('div', $arProperty['NAME'].':', [
                                                        'class' => [
                                                            'catalog-section-item-properties-item-name',
                                                            'catalog-section-item-properties-item-part'
                                                        ]
                                                    ]) ?>
                                                    <?= Html::tag('div', $arProperty['DISPLAY_VALUE'], [
                                                        'class' => [
                                                            'catalog-section-item-properties-item-value',
                                                            'catalog-section-item-properties-item-part'
                                                        ]
                                                    ]) ?>
                                                </div>
                                                <?php $iCount++ ?>
                                            <?php } ?>
                                        </div>
                                        <?php unset($iCount); ?>
                                    <?php } ?>
                                    <?php if ($arData['PRICE']['CURRENT']['SHOW']) { ?>
                                        <div class="catalog-section-item-price">
                                            <div class="catalog-section-item-price-container">
                                                <div class="catalog-section-item-price-item catalog-section-item-price-current">
                                                    <?= $arData['PRICE']['CURRENT']['VALUE'] ?>
                                                </div>
                                                <?php if ($arData['PRICE']['OLD']['SHOW']) { ?>
                                                    <div class="catalog-section-item-price-item catalog-section-item-price-old">
                                                        <?= $arData['PRICE']['OLD']['VALUE'] ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!-- items-container -->
            <?php if ($arVisual['NAVIGATION']['LAZY']['BUTTON']) { ?>
                <!-- noindex -->
                    <div class="catalog-section-more" data-use="show-more-<?= $arNavigation['NavNum'] ?>">
                        <div class="catalog-section-more-button">
                            <div class="catalog-section-more-icon intec-cl-background">
                                <i class="glyph-icon-show-more"></i>
                            </div>
                            <div class="catalog-section-more-text intec-cl-text">
                                <?= Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_TEMPLATE_LAZY_LOAD_TEXT') ?>
                            </div>
                        </div>
                    </div>
                <!-- /noindex -->
            <?php } ?>
            <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
                <div class="catalog-section-navigation-bottom" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
                    <!-- pagination-container -->
                    <?= $arResult['NAV_STRING'] ?>
                    <!-- pagination-container -->
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>