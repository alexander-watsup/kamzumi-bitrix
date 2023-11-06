<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$iCounter = 0;

$bigItem = 1;
$twoItemsStart = 2;
$twoItemsEnd = 3;
?>
<div class="widget c-gallery c-gallery-template-5" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => 'widget-content',
                'data' => [
                    'indent' => $arVisual['INDENT']['USE'] ? $arVisual['INDENT']['VALUE'] : null
                ]
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items' => true,
                        'intec-grid' => [
                            '' => true,
                            'wrap' => true,
                            'a-v-stretch' => true
                        ]
                    ], true),
                    'data' => [
                        'role' => 'items'
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        $sPictureResized = CFile::ResizeImageGet($sPicture, [
                            'width' => 900,
                            'height' => 900
                        ], BX_RESIZE_IMAGE_EXACT);

                        if (!empty($sPictureResized)) {
                            $sPictureResized = $sPictureResized['src'];
                        } else {
                            $sPictureResized = $sPicture['SRC'];
                        }

                        $sPicture = $sPicture['SRC'];

                        $iCounter++;

                        if ($iCounter % 8 == 0)
                            $bigItem = $iCounter;
                        ?>

                        <?php if ($iCounter == $twoItemsStart) { ?>
                            <div class="intec-grid-item-4 intec-grid-item-768-1">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch">
                        <?php } ?>

                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-item-big' => $iCounter == $bigItem || $iCounter == $bigItem+1,
                                    'intec-grid-item-auto' => $iCounter == $bigItem || $iCounter == $bigItem+1,
                                    'intec-grid-item-1' => $iCounter == $twoItemsStart || $iCounter == $twoItemsEnd,
                                    'intec-grid-item-4' => !($iCounter == $bigItem) && !($iCounter == $bigItem+1) && !($iCounter == $twoItemsStart) && !($iCounter == $twoItemsEnd),
                                    'widget-item-long' => !($iCounter == $bigItem) && !($iCounter == $bigItem+1) && !($iCounter == $twoItemsStart) && !($iCounter == $twoItemsEnd),
                                    'intec-grid-item-768-1' => true
                                ], true)
                            ]) ?>
                                <div class="widget-item-wrapper" id="<?= $sAreaId ?>">
                                    <?= Html::beginTag('div', [
                                        'class' => 'widget-item-picture-wrap',
                                        'data' => [
                                            'role' => 'item',
                                            'src' => $sPicture,
                                            'preview-src' => $sPictureResized
                                        ]
                                    ]) ?>
                                        <?= Html::beginTag('div', [
                                            'class' => 'widget-item-picture',
                                            'title' => Html::decode(Html::stripTags($arItem['NAME'])),
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPictureResized : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPictureResized.'\')' : null
                                            ]
                                        ]) ?>
                                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPictureResized, [
                                                'alt' => $arItem['NAME'],
                                                'title' => $arItem['NAME'],
                                                'loading' => 'lazy',
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPictureResized : null
                                                ]
                                            ]) ?>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?= Html::endTag('div') ?>

                        <?php if ($iCounter == $twoItemsEnd) {?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                            if ($iCounter == $twoItemsStart)
                                $twoItemsStart = $twoItemsStart + 4;

                            if ($iCounter == $twoItemsEnd)
                                $twoItemsEnd = $twoItemsEnd + 4;
                        ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>

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
<?php include(__DIR__.'/parts/script.php') ?>