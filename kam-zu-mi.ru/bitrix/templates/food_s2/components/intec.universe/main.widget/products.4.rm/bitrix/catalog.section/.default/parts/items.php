<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$iColumns = $arVisual['COLUMNS']['DESKTOP'];

if (!empty($arBanner))
    if ($iColumns <= 3)
        $iColumns = 4;

$iItemsCount = null;
$iItemsCurrent = 0;

if ($arVisual['LINES'] !== null)
    $iItemsCount = $iColumns * $arVisual['LINES'];

if (!empty($arBanner))
    if ($iItemsCount >= 4) {
        $iItemsCount = $iItemsCount - 2;
    } else {
        $iItemsCount--;
    }

?>
<?= Html::beginTag('div', [
    'class' => Html::cssClassFromArray([
        'widget-items' => true,
        'intec-grid' => [
            '' => true,
            'wrap' => true,
            'a-v-stretch' => true,
            'a-h-start' => true,
            'i-10' => $arVisual['INDENTS']['USE']
        ]
    ], true)
]) ?>
<?php if (!empty($arBanner)) {
    $sId = $sTemplateId.'_'.$arBanner['ID'];
    $sAreaId = $this->GetEditAreaId($sId);
    $this->AddEditAction($sId, $arBanner['EDIT_LINK']);
    $this->AddDeleteAction($sId, $arBanner['DELETE_LINK']);

    $sData = Json::encode($dData($arBanner), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);
    $sLink = Html::decode($arBanner['DETAIL_PAGE_URL']);

?>
    <?= Html::beginTag('div', [
        'id' => $sAreaId,
        'class' => Html::cssClassFromArray([
            'widget-item' => true,
            'intec-grid-item' => [
                'auto' => $arVisual['COLUMNS']['DESKTOP'] >= 5,
                '2' => $arVisual['COLUMNS']['DESKTOP'] < 5,
                '1200-auto' => true,
                '950-1' => true
            ]
        ],  true),
        'data' => [
            'id' => $arBanner['ID'],
            'role' => 'item',
            'data' => $sData,
            'expanded' => 'false',
            'available' => $arBanner['CAN_BUY'] ? 'true' : 'false',
            'first-item' => 'true',
            'first-item-theme' => $arBanner['BANNER']['THEME'],
            'columns' => $arVisual['COLUMNS']['DESKTOP']
        ]
    ]) ?>
    <div class="widget-item-wrapper">
        <div class="widget-item-first">
            <div class="widget-item-image-container">
                <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', '', [
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
                    'class' => [
                        'widget-item-big-link',
                    ],
                    'data' => [
                        'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                    ]
                ]) ?>
                <?php $vBigImage($arBanner) ?>
                <!--noindex-->
                <div class="widget-item-marks">
                    <?php $APPLICATION->includeComponent(
                        'intec.universe:main.markers',
                        'template.2', [
                        'HIT' => $arBanner['MARKS']['HIT'] ? 'Y' : 'N',
                        'NEW' => $arBanner['MARKS']['NEW'] ? 'Y' : 'N',
                        'RECOMMEND' => $arBanner['MARKS']['RECOMMEND'] ? 'Y' : 'N',
                        'ORIENTATION' => $arVisual['MARKS']['ORIENTATION']
                    ],
                        $component,
                        ['HIDE_ICONS' => 'Y']
                    ) ?>
                </div>
                <!--/noindex-->
                <div class="widget-item-info">
                    <?php if ($arVisual['VOTE']['SHOW']) { ?>
                        <div class="widget-item-vote">
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:iblock.vote',
                                'template.1',
                                array(
                                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                    'ELEMENT_ID' => $arBanner['ID'],
                                    'ELEMENT_CODE' => $arBanner['CODE'],
                                    'MAX_VOTE' => '5',
                                    'VOTE_NAMES' => array(
                                        0 => '1',
                                        1 => '2',
                                        2 => '3',
                                        3 => '4',
                                        4 => '5',
                                    ),
                                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                                    'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                    'SHOW_RATING' => 'N'
                                ),
                                $component,
                                ['HIDE_ICONS' => 'Y']
                            ) ?>
                        </div>
                    <?php } ?>
                    <!--/noindex-->
                    <div class="widget-item-name">
                        <div>
                            <?= $arBanner['NAME'] ?>
                        </div>
                    </div>
                    <!--noindex-->
                    <?php if ($arVisual['QUANTITY']['SHOW'] || $arVisual['ARTICLE']['SHOW']) { ?>
                        <div class="widget-item-information">
                            <div class="intec-grid intec-grid-wrap intec-grid-i-h-8 intec-grid-i-v-2">
                                <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                    <div class="widget-item-quantity-wrap intec-grid-item-auto">
                                        <?php $vQuantity($arBanner) ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['ARTICLE']['SHOW']) { ?>
                                    <?= Html::beginTag('div', [
                                        'class' => 'widget-item-article-wrap intec-grid-item-auto',
                                        'data' => [
                                            'role' => 'article',
                                            'show' => !empty($arBanner['DATA']['ARTICLE']['VALUE']) ? 'true' : 'false'
                                        ]
                                    ]) ?>
                                    <div class="widget-item-article">
                                        <?= Html::tag('span', $arBanner['DATA']['ARTICLE']['NAME'], [
                                            'class' => 'widget-item-article-name'
                                        ]) ?>
                                        <?= Html::tag('span', $arBanner['DATA']['ARTICLE']['VALUE'], [
                                            'class' => 'widget-item-article-value',
                                            'data-role' => 'article.value'
                                        ]) ?>
                                    </div>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <!--/noindex-->
                    <?php $vPrice($arBanner) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php foreach ($arItems as $arItem) {

    if ($iItemsCount !== null)
        if ($iItemsCurrent >= $iItemsCount)
            break;

    $sId = $sTemplateId.'_'.$arItem['ID'];
    $sAreaId = $this->GetEditAreaId($sId);
    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

    $sData = Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);
    $sLink = Html::decode($arItem['DETAIL_PAGE_URL']);

    $arSkuProps = [];

    if (!empty($arResult['SKU_PROPS']))
        $arSkuProps = $arResult['SKU_PROPS'];
    else if (!empty($arItem['SKU_PROPS']))
        $arSkuProps = $arItem['SKU_PROPS'];

    $bRecalculation = false;

    if ($bBase && $arVisual['PRICE']['RECALCULATION']) {
        if ($arVisual['COUNTER']['SHOW'] && $arItem['ACTION'] === 'buy')
            $bRecalculation = true;
    }
    ?>
    <?= Html::beginTag('div', [
        'id' => $sAreaId,
        'class' => Html::cssClassFromArray([
            'widget-item' => true,
            'intec-grid-item' => [
                $iColumns => true,
                '500-1' => ($iColumns <= 5) && $arVisual['COLUMNS']['MOBILE'] == 1,
                '500-2' => ($iColumns <= 5) && $arVisual['COLUMNS']['MOBILE'] == 2,
                '700-2' => $iColumns > 2,
                '950-3' => $iColumns > 2,
                '1200-3' => $iColumns > 3
            ]
        ],  true),
        'data' => [
            'id' => $arItem['ID'],
            'role' => 'item',
            'data' => $sData,
            'expanded' => 'false',
            'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
            'recalculation' => $bRecalculation ? 'true' : 'false'
        ]
    ]) ?>
        <div class="widget-item-wrapper" data-borders-style="<?= $arVisual['BORDERS']['STYLE'] ?>">
            <div class="widget-item-base">
                <?php $vButtons($arItem) ?>
                <div class="widget-item-image-container">
                    <?php $vImage($arItem) ?>
                    <!--noindex-->
                    <div class="widget-item-marks">
                        <?php $APPLICATION->includeComponent(
                            'intec.universe:main.markers',
                            'template.2', [
                            'HIT' => $arItem['MARKS']['HIT'] ? 'Y' : 'N',
                            'NEW' => $arItem['MARKS']['NEW'] ? 'Y' : 'N',
                            'RECOMMEND' => $arItem['MARKS']['RECOMMEND'] ? 'Y' : 'N',
                            'ORIENTATION' => $arVisual['MARKS']['ORIENTATION']
                        ],
                            $component,
                            ['HIDE_ICONS' => 'Y']
                        ) ?>
                    </div>
                    <!--/noindex-->
                </div>
                <!--noindex-->
                <?php if ($arVisual['VOTE']['SHOW']) { ?>
                    <div class="widget-item-vote">
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:iblock.vote',
                            'template.1',
                            array(
                                'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                'ELEMENT_ID' => $arItem['ID'],
                                'ELEMENT_CODE' => $arItem['CODE'],
                                'MAX_VOTE' => '5',
                                'VOTE_NAMES' => array(
                                    0 => '1',
                                    1 => '2',
                                    2 => '3',
                                    3 => '4',
                                    4 => '5',
                                ),
                                'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                'CACHE_TIME' => $arParams['CACHE_TIME'],
                                'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                'SHOW_RATING' => 'N'
                            ),
                            $component,
                            ['HIDE_ICONS' => 'Y']
                        ) ?>
                    </div>
                <?php } ?>
                <!--/noindex-->
                <div class="widget-item-name">
                    <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
                        'class' => [
                            'intec-cl-text-hover',
                        ],
                        'data' => [
                            'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                        ]
                    ]) ?>
                </div>
                <!--noindex-->
                <?php if ($arVisual['QUANTITY']['SHOW'] || $arVisual['ARTICLE']['SHOW']) { ?>
                    <div class="widget-item-information">
                        <div class="intec-grid intec-grid-wrap intec-grid-i-h-8 intec-grid-i-v-2">
                            <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                <div class="widget-item-quantity-wrap intec-grid-item-auto">
                                    <?php $vQuantity($arItem) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['ARTICLE']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'widget-item-article-wrap intec-grid-item-auto',
                                    'data' => [
                                        'role' => 'article',
                                        'show' => !empty($arItem['DATA']['ARTICLE']['VALUE']) ? 'true' : 'false'
                                    ]
                                ]) ?>
                                <div class="widget-item-article">
                                    <?= Html::tag('span', $arItem['DATA']['ARTICLE']['NAME'], [
                                        'class' => 'widget-item-article-name'
                                    ]) ?>
                                    <?= Html::tag('span', $arItem['DATA']['ARTICLE']['VALUE'], [
                                        'class' => 'widget-item-article-value',
                                        'data-role' => 'article.value'
                                    ]) ?>
                                </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS']) && !empty($arSkuProps)) {
                    $vSku($arSkuProps);
                } ?>
                <!--/noindex-->
                <?php $vPrice($arItem) ?>
            </div>
            <!--noindex-->
            <div class="widget-item-advanced">
                <?php if ($arItem['ACTION'] !== 'none') { ?>
                    <div class="widget-item-purchase-container">
                        <div class="widget-item-purchase-container-wrapper intec-grid intec-grid-a-v-center">
                            <?php if ($arVisual['COUNTER']['SHOW'] && $arItem['ACTION'] === 'buy') {
                                $vCounter();
                            } ?>
                            <div class="widget-item-purchase intec-grid-item intec-grid-item-shrink-1">
                                <?php $vPurchase($arItem) ?>
                            </div>
                        </div>
                        <?php if ($bRecalculation) { ?>
                            <div class="widget-item-summary hidden" data-role="item.summary">
                                <?= Loc::getMessage('C_WIDGET_PRODUCTS_4_TITLE_SUMMARY') ?>
                                <span data-role="item.summary.price"></span>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <!--/noindex-->
        </div>
    <?= Html::endTag('div') ?>
    <?php $iItemsCurrent++; ?>
    <?php $bFirstItem = false; ?>
<?php } ?>
<?= Html::endTag('div') ?>
