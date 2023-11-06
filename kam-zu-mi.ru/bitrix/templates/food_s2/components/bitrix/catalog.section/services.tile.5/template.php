<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
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
<div class="ns-bitrix c-catalog-section c-catalog-section-services-tile-5" id="<?= $sTemplateId ?>">
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
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-section-content',
                    'intec-grid' => [
                        '',
                        'wrap',
                        'a-v-stretch',
                        'i-16'
                    ]
                ],
                'data-entity' => $sTemplateContainer
            ]) ?>
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $arData = $arItem['DATA'];

                    $sPicture = $arItem['PREVIEW_PICTURE'];

                    if (!empty($sPicture)) {
                        $sPicture = CFile::ResizeImageGet($sPicture, [
                            'width' => 550,
                            'height' => 550
                        ], BX_RESIZE_IMAGE_EXACT);

                        if (!empty($sPicture))
                            $sPicture = $sPicture['src'];
                    }

                    if (empty($sPicture))
                        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'intec-grid-item' => [
                                $arVisual['COLUMNS'] => true,
                                '1024-3' => $arVisual['COLUMNS'] >= 4,
                                '768-2' => true,
                                '500-1' => true
                            ]
                        ], true),
                        'data-entity' => 'items-row'
                    ]) ?>
                        <div class="catalog-section-item" id="<?= $sAreaId ?>" data-role="item">
                            <div class="catalog-section-item-content">
                                <?= Html::tag('a', null, [
                                    'class' => [
                                        'catalog-section-item-picture',
                                        'intec-image-effect'
                                    ],
                                    'title' => $arItem['NAME'],
                                    'href' => $arItem['DETAIL_PAGE_URL'],
                                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                    'data-role' => 'picture',
                                    'style' => [
                                        'background-image' => $arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arVisual['LAZYLOAD']['STUB'].'\')' : 'url(\''.$sPicture.'\')'
                                    ]
                                ]) ?>
                                <div class="catalog-section-item-information" data-role="information">
                                    <div class="catalog-section-item-information-content" data-role="information.content">
                                        <?php if ($arData['PRICE']['CURRENT']['SHOW']) { ?>
                                            <div class="catalog-section-item-price">
                                                <div class="catalog-section-item-price-item">
                                                    <?= Html::tag('div', $arData['PRICE']['CURRENT']['VALUE'], [
                                                        'class' => [
                                                            'catalog-section-item-price-current',
                                                            'catalog-section-item-price-item-part'
                                                        ]
                                                    ]) ?>
                                                    <?php if ($arData['PRICE']['OLD']['SHOW']) { ?>
                                                        <?= Html::tag('div', $arData['PRICE']['OLD']['VALUE'], [
                                                            'class' => [
                                                                'catalog-section-item-price-old',
                                                                'catalog-section-item-price-item-part'
                                                            ]
                                                        ]) ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php } ?>
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
                                                                'catalog-section-item-properties-name',
                                                                'catalog-section-item-properties-item-part'
                                                            ]
                                                        ]) ?>
                                                        <?= Html::tag('div', $arProperty['DISPLAY_VALUE'], [
                                                            'class' => [
                                                                'catalog-section-item-properties-value',
                                                                'catalog-section-item-properties-item-part'
                                                            ]
                                                        ]) ?>
                                                    </div>
                                                    <?php $iCount++ ?>
                                                <?php } ?>
                                            </div>
                                            <?php unset($iCount); ?>
                                        <?php } ?>
                                        <div class="catalog-section-item-name">
                                            <?= Html::tag('a', $arItem['NAME'], [
                                                'class' => 'intec-cl-text-hover',
                                                'href' => $arItem['DETAIL_PAGE_URL']
                                            ]) ?>
                                        </div>
                                        <?php if ($arVisual['PREVIEW']['SHOW'] && !empty($arItem['PREVIEW_TEXT'])) { ?>
                                            <?= Html::tag('div', $arItem['PREVIEW_TEXT'], [
                                                'class' => 'catalog-section-item-description',
                                                'title' => $arItem['PREVIEW_TEXT']
                                            ]) ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
            <!-- items-container -->
            <?php if ($arVisual['NAVIGATION']['LAZY']['BUTTON']) { ?>
                <!-- noindex -->
                    <div class="catalog-section-more" data-use="show-more-<?= $arNavigation['NavNum'] ?>">
                        <div class="catalog-section-more-button">
                            <div class="catalog-section-more-icon intec-cl-background">
                                <i class="glyph-icon-show-more"></i>
                            </div>
                            <div class="catalog-section-more-text intec-cl-text">
                                <?= Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_TEMPLATE_LAZY_LOAD_TEXT') ?>
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