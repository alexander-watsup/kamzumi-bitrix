<?php if (defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var $arResult
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
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-services-list-1'
    ],
    'data' => [
        'rounded' => $arVisual['ROUNDED'] ? 'true' : 'false',
        'wide' => $arVisual['WIDE'] ? 'true' : 'false'
    ]
]) ?>
    <?php if ($arVisual['NAVIGATION']['TOP']['SHOW']) { ?>
        <div class="catalog-section-navigation catalog-section-navigation-top" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
            <!-- pagination-container -->
            <?= $arResult['NAV_STRING'] ?>
            <!-- pagination-container -->
        </div>
    <?php } ?>
    <!-- items-container -->
    <?= Html::beginTag('div', [
         'class' => [
             'catalog-section-items',
             'intec-grid' => [
                 '',
                 'wrap',
                 'a-h-start',
                 'a-v-start',
                 'i-10'
             ]
         ],
        'data' => [
            'entity' => $sTemplateContainer
        ]
    ]) ?>
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
        <?php
            $sId = $sTemplateId.'_'.$arItem['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

            $sName = $arItem['NAME'];
            $sLink = $arItem['DETAIL_PAGE_URL'];
            $sDescription = $arItem['PREVIEW_TEXT'];
            $sPicture = $arItem['PREVIEW_PICTURE'];

            if (!empty($sDescription))
                $sDescription = Html::stripTags($sDescription);

            if (!empty($sDescription))
                $sDescription = StringHelper::truncate($sDescription, 100);

            if (empty($sPicture))
                $sPicture = $arItem['DETAIL_PICTURE'];

            if (!empty($sPicture)) {
                $sPicture = CFile::ResizeImageGet($sPicture, [
                    'width' => 350,
                    'height' => 350
                ], BX_RESIZE_IMAGE_PROPORTIONAL);

                if (!empty($sPicture))
                    $sPicture = $sPicture['src'];
            }

            if (empty($sPicture))
                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

        ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'catalog-section-item' => true,
                    'intec-grid-item' => [
                        '2' => true,
                        '1200-1' => !$arVisual['WIDE'],
                        '850-1' => $arVisual['WIDE']
                    ]
                ], true),
                'data' => [
                    'entity' => 'items-row'
                ]
            ]) ?>
                <div id="<?= $sAreaId ?>" class="catalog-section-item-wrapper">
                    <div class="catalog-section-item-picture">
                        <?= Html::tag('a', null, [
                            'href' => $sLink,
                            'class' => 'intec-image-effect',
                            'data' => [
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                            ],
                            'style' => [
                                'background-image' => $arVisual['LAZYLOAD']['USE'] ? null : 'url(\''.$sPicture.'\')'
                            ]
                        ]) ?>
                    </div>
                    <div class="catalog-section-item-information">
                        <a href="<?= $sLink ?>" class="catalog-section-item-name">
                            <?= $sName ?>
                        </a>
                        <?php if (!empty($sDescription)) { ?>
                            <div class="catalog-section-item-description">
                                <?= $sDescription ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="intec-ui-clear"></div>
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
                    <?= Loc::getMessage('C_BITRIX_CATALOG_SECTION_SERVICES_LIST_1_LAZY_TEXT') ?>
                </div>
            </div>
        </div>
        <!-- /noindex -->
    <?php } ?>
    <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
        <div class="catalog-section-navigation catalog-section-navigation-bottom" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
            <!-- pagination-container -->
            <?= $arResult['NAV_STRING'] ?>
            <!-- pagination-container -->
        </div>
    <?php } ?>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>