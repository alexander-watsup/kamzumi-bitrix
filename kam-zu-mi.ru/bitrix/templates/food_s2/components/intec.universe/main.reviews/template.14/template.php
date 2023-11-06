<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

/**
 * @var Closure $vPicture()
 */
include(__DIR__.'/parts/picture.php');

?>
<div class="widget c-reviews c-reviews-template-18" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-a-h-between intec-grid-i-v-7 intec-grid-i-h-5">
                            <div class="widget-title intec-grid-item-auto">
                                <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                            </div>
                            <?php if ($arVisual['BUTTON_SHOW_ALL']['SHOW']) { ?>
                                <?= Html::tag('a', $arVisual['BUTTON_SHOW_ALL']['TEXT'], [
                                    'href' => $arVisual['BUTTON_SHOW_ALL']['LINK'],
                                    'class' => [
                                        'widget-link-all',
                                        'intec-grid-item-auto',
                                        'intec-cl-text-hover'
                                    ]
                                ]) ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="widget-description">
                            <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items' => true,
                        'owl-carousel' => $arVisual['SLIDER']['USE'],
                        'intec-grid' => [
                            '' => !$arVisual['SLIDER']['USE'],
                            'wrap' => !$arVisual['SLIDER']['USE'],
                            'a-v-stretch' => !$arVisual['SLIDER']['USE'],
                            'i-h-15' => !$arVisual['SLIDER']['USE'],
                            'i-v-25' => !$arVisual['SLIDER']['USE']
                        ]
                    ], true),
                    'data' => [
                        'role' => 'container',
                        'grid' => 1,
                        'slider' => $arVisual['SLIDER']['USE'] ? 'true' : 'false'
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $arData = $arItem['DATA'];

                        if (!empty($arItem['PREVIEW_TEXT'])) {
                            $sText = $arItem['PREVIEW_TEXT'];
                        } elseif (!empty($arItem['DETAIL_TEXT'])) {
                            $sText = $arItem['DETAIL_TEXT'];
                        } else
                            continue;

                        $sTag = !empty($arItem['DETAIL_PAGE_URL']) && $arVisual['LINK']['USE'] ? 'a' : 'div';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item-1' => true
                            ], true)
                        ]) ?>
                            <div class="widget-item-wrapper intec-grid intec-grid-768-wrap intec-grid-a-v-center intec-grid-i-h-15" id="<?= $sAreaId ?>">
                                <div class="widget-item-picture-wrap intec-grid-item-2 intec-grid-item-768-1">
                                    <?php $vPicture($arItem) ?>
                                </div>
                                <div class="widget-item-text intec-grid-item-2 intec-grid-item-768-1">
                                    <div class="widget-item-content">
                                        <div class="widget-item-description">
                                            <div class="widget-item-description-quote intec-cl-svg-path-stroke">
                                                <?= FileHelper::getFileData(__DIR__.'/images/quote_icon.svg')?>
                                            </div>
                                            <div class="widget-item-description-text">
                                                <?= $sText ?>
                                            </div>
                                        </div>
                                        <div class="intec-grid intec-grid-a-v-center intec-grid-a-h-between intec-grid-i-h-5 intec-grid-i-v-5 intec-grid-wrap">
                                            <div class="intec-grid-item-auto">
                                                <div class="widget-item-name-wrap">
                                                    <?= Html::tag($sTag, $arItem['NAME'], [
                                                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                                        'class' => 'widget-item-name',
                                                        'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                                    ]) ?>
                                                </div>
                                                <?php if ($arVisual['RATING']['SHOW'] && !empty($arData['RATING'])) {?>
                                                    <?= Html::beginTag('div', [
                                                        'class' => Html::cssClassFromArray([
                                                            'widget-item-rating-wrap' => true,
                                                            'intec-grid' => [
                                                                '' => true,
                                                                'i-h-3' => true
                                                            ]
                                                        ], true)
                                                    ])?>
                                                    <?php for ($countStar=1;$countStar<=$arVisual['RATING']['MAX'];$countStar++) { ?>
                                                        <?= Html::beginTag('div', [
                                                            'class' => Html::cssClassFromArray([
                                                                'intec-grid-item-auto' => true,
                                                                'widget-item-rating' => [
                                                                    '' => true,
                                                                    'active' => ($countStar<=$arData['RATING'])
                                                                ]
                                                            ], true)
                                                        ])?>
                                                        <i class="intec-ui-icon intec-ui-icon-star-1"></i>
                                                        <?= Html::endTag('div') ?>
                                                    <?php } ?>
                                                    <?= Html::endTag('div') ?>
                                                <?php } ?>
                                            </div>
                                            <?php if ($arVisual['LINK']['USE'] && !empty($arParams['LINK_TEXT'])) { ?>
                                                <div class="widget-item-link-detail intec-grid-item-auto">
                                                    <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="widget-item-link-detail-button intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-scheme-current">
                                                        <?= $arParams['LINK_TEXT'] ?>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
                <div class="widget-items-dots-wrap">
                    <div class="widget-items-dots intec-grid  intec-grid-wrap">
                        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                            <div class="widget-items-dot intec-grid-item-4">
                                <div class="widget-dot-picture-wrap intec-cl-border">
                                    <?php $vPicture($arItem) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
                <div class="widget-footer align-<?= $arBlocks['FOOTER']['POSITION'] ?>">
                    <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                        <?= Html::tag('a', $arBlocks['FOOTER']['BUTTON']['TEXT'], [
                            'href' => $arBlocks['FOOTER']['BUTTON']['LINK'],
                            'class' => [
                                'widget-footer-button',
                                'intec-ui' => [
                                    '',
                                    'size-5',
                                    'scheme-current',
                                    'control-button',
                                    'mod' => [
                                        'transparent',
                                        'round-half'
                                    ]
                                ]
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php if ($arVisual['VIDEO']['SHOW'] || $arVisual['SLIDER']['USE'])
    include(__DIR__.'/parts/script.php');
?>