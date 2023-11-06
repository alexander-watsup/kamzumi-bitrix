<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
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

?>
<div class="widget c-reviews c-reviews-template-15" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-header-content intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-a-h-center intec-grid-a-h-600-between intec-grid-i-v-7">
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
                                    'width' => 128,
                                    'height' => 128
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
                        <div class="widget-item" id="<?= $sAreaId ?>">
                            <div class="widget-item-wrapper">
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
                                <div class="widget-item-content">
                                    <div class="widget-item-description">
                                        <?= $sText ?>
                                    </div>
                                    <?= Html::tag($sTag, $arItem['NAME'], [
                                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                        'class' => 'widget-item-name',
                                        'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                    ]) ?>
                                    <?php if ($arVisual['RATING']['SHOW'] && !empty($arData['RATING'])) {?>
                                        <div class="widget-item-rating-wrap intec-grid intec-grid-i-h-3 intec-grid-a-h-center">
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
                            </div>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
                <div class="widget-slide-content">

                </div>
                <div class="widget-navigation">
                    <span class="widget-button-left intec-cl-background-hover intec-cl-border-hover">
                        <i class="far fa-angle-left"></i>
                    </span>
                    <span class="widget-button-right intec-cl-background-hover intec-cl-border-hover">
                        <i class="far fa-angle-right"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include(__DIR__.'/parts/script.php');