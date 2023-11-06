<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
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

?>
<div class="widget c-reviews c-reviews-template-16" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-a-h-between intec-grid-i-v-7">
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
                    'class' => [
                        'widget-items',
                        'owl-carousel'
                    ],
                    'data' => [
                        'role' => 'container'
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $arData = $arItem['DATA'];
                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                    'width' => 64,
                                    'height' => 64
                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                            );

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        if (!empty($arItem['PREVIEW_TEXT'])) {
                            $sText = $arItem['PREVIEW_TEXT'];
                        } elseif (!empty($arItem['DETAIL_TEXT'])) {
                            $sText = $arItem['DETAIL_TEXT'];
                        } else
                            continue;

                        $sTag = !empty($arItem['DETAIL_PAGE_URL']) && $arVisual['LINK']['USE'] ? 'a' : 'div';
                    ?>
                        <div class="widget-item intec-grid-item" id="<?= $sAreaId ?>">
                            <div class="widget-item-wrapper intec-cl-background-hover intec-grid intec-grid-o-vertical intec-grid-a-h-between">
                                <div class="widget-item-description intec-grid-item-auto">
                                    <div class="widget-item-description-quote intec-cl-svg-path-stroke">
                                        <?= FileHelper::getFileData(__DIR__.'/images/quote_icon.svg')?>
                                    </div>
                                    <div class="widget-item-description-text">
                                        <?= $sText ?>
                                    </div>
                                </div>
                                <?php if ($arVisual['RATING']['SHOW'] && !empty($arData['RATING'])) {?>
                                    <div class="widget-item-rating-wrap intec-grid-item-auto intec-grid intec-grid-a-h-center intec-grid-i-h-3">
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
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="widget-item-info">
                                <div class="widget-item-image-wrap">
                                    <?= Html::tag($sTag, '', [
                                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                        'class' => 'widget-item-image',
                                        'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                        ],
                                        'style' => [
                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                        ]
                                    ]) ?>
                                </div>
                                <?= Html::tag($sTag, $arItem['NAME'], [
                                    'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                    'class' => 'widget-item-name',
                                    'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                ]) ?>
                                <?php if ($arVisual['ACTIVE_DATE']['SHOW']) { ?>
                                    <div class="widget-item-date">
                                        <?= $arItem['DISPLAY_ACTIVE_FROM'];?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>
<?php include(__DIR__.'/parts/script.php');