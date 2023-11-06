<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arBlocks = $arResult['BLOCKS'];
$arNavigation = $arResult['NAVIGATION'];

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

?>
<div class="widget c-news c-news-template-8" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
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
            <div class="widget-content">
                <div class="widget-items" data-role="items">
                    <!--items-->
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $arData = $arItem['DATA'];

                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if ($arVisual['PICTURE']['SHOW']) {
                            if (empty($sPicture))
                                $sPicture = $arItem['DETAIL_PICTURE'];

                            if (!empty($sPicture)) {
                                $sPicture = CFile::ResizeImageGet($sPicture, [
                                    'width' => 150,
                                    'height' => 150
                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                if (!empty($sPicture))
                                    $sPicture = $sPicture['src'];
                            }

                            if (empty($sPicture))
                                $sPicture = SITE_TEMPLATE_PATH . '/images/picture.missing.png';
                        }

                    ?>
                        <div class="widget-item" id="<?= $sAreaId ?>">
                            <div class="intec-grid intec-grid-i-h-12 intec-grid-i-v-8 intec-grid-550-wrap">
                                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto intec-grid-item-550-1">
                                        <?= Html::beginTag($sTag, [
                                            'class' => 'widget-item-picture',
                                            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                            'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                        ]) ?>
                                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                                'class' => 'intec-image-effect',
                                                'alt' => $arItem['NAME'],
                                                'title' => $arItem['NAME'],
                                                'loading' => 'lazy',
                                                'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ]) ?>
                                        <?= Html::endTag($sTag) ?>
                                    </div>
                                <?php } ?>
                                <div class="intec-grid-item intec-grid-item-550-1">
                                    <?php if ($arVisual['DATE']['SHOW'] || $arData['CATEGORY']['SHOW']) { ?>
                                        <div class="widget-item-header">
                                            <?php if ($arVisual['DATE']['SHOW'] && !$arData['CATEGORY']['SHOW']) { ?>
                                                <?= $arData['DATE'] ?>
                                            <?php } else if (!$arVisual['DATE']['SHOW'] && $arData['CATEGORY']['SHOW']) { ?>
                                                <?= $arData['CATEGORY']['VALUE'] ?>
                                            <?php } else if ($arVisual['DATE']['SHOW'] && $arData['CATEGORY']['SHOW']) { ?>
                                                <?= Loc::getMessage('C_MAIN_NEWS_TEMPLATE_8_TEMPLATE_DATE_CATEGORY', [
                                                    '#DATE#' => $arData['DATE'],
                                                    '#CATEGORY#' => $arData['CATEGORY']['VALUE']
                                                ]) ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <div class="widget-item-name">
                                        <?php if ($sTag === 'a') { ?>
                                            <?= Html::tag('a', $arItem['NAME'], [
                                                'class' => 'intec-cl-text-hover',
                                                'href' => $arItem['DETAIL_PAGE_URL'],
                                                'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                                            ]) ?>
                                        <?php } else { ?>
                                            <?= Html::tag('span', $arItem['NAME']) ?>
                                        <?php } ?>
                                    </div>
                                    <?php if ($arData['PREVIEW']['SHOW']) { ?>
                                        <div class="widget-item-description">
                                            <?= $arData['PREVIEW']['VALUE'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!--items-->
                </div>
            </div>
            <?php if ($arNavigation['USE']) { ?>
                <div class="widget-pagination" data-role="pagination">
                    <!--navigation-->
                    <?= $arNavigation['PRINT'] ?>
                    <!--navigation-->
                </div>
            <?php } ?>
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
    <?php if ($arNavigation['USE'] && $arNavigation['MODE'] === 'ajax' && !defined('EDITOR'))
        include(__DIR__.'/parts/script.php');
    ?>
</div>