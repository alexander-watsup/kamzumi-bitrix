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

?>
<div class="widget c-categories c-categories-template-1" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
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
                <div class="widget-items intec-grid intec-grid-wrap intec-grid-a-v-stretch intec-grid-i-5">
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
                            $sPicture = CFile::ResizeImageGet(
                                $sPicture, [
                                'width' => 500,
                                'height' => 500
                            ],
                                BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                            );

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        if ($arVisual['LINK']['USE'] && !empty($arItem['DETAIL_PAGE_URL']))
                            $sTag = 'a';
                        else
                            $sTag = 'div';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1024-3' => $arVisual['COLUMNS'] >= 3,
                                    '768-2' => true,
                                    '500-1' => true
                                ]
                            ], true)
                        ]) ?>
                            <?= Html::beginTag($sTag, [
                                'id' => $sAreaId,
                                'class' => [
                                    'widget-item-wrapper',
                                    'intec-cl-border-hover'
                                ],
                                'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                            ]) ?>
                                <div class="widget-item-picture-wrap">
                                    <?= Html::tag('div', '', [
                                        'class' => 'widget-item-picture',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                        ],
                                        'style' => [
                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                        ]
                                    ]) ?>
                                </div>
                                <div class="widget-item-name">
                                    <?= $arItem['NAME'] ?>
                                </div>
                            <?= Html::endTag($sTag) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
