<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEM']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$sId = $sTemplateId.'_'.$arResult['ITEM']['ID'];
$sAreaId = $this->GetEditAreaId($sId);
$this->AddEditAction($sId, $arResult['ITEM']['EDIT_LINK']);
$this->AddDeleteAction($sId, $arResult['ITEM']['DELETE_LINK']);

$sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
if (!empty($arResult['PICTURE']['SRC'])) {
    $fPicture = CFile::ResizeImageGet(
        $arResult['PICTURE'],
        [
            'width' => 500,
            'height' => 500
        ],
        BX_RESIZE_IMAGE_PROPORTIONAL,
        true
    );

    $sPicture = $fPicture['src'];
}

/**
 * @var Closure $vAdvantages($arData)
 * @var Closure $vPicture($sPicture, $sVideo)
 * @var Closure $vButton($sLink)
 */
$vPicture = include(__DIR__.'/parts/picture.php');
$vButton = include(__DIR__.'/parts/button.php');
$vAdvantages = include(__DIR__.'/parts/advantages.php');
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-about',
        'c-about-template-1'
    ],
    'data' => [
        'view' => $arVisual['VIEW']
    ]
]); ?>
    <div class="widget-content">
        <div class="widget-item" id="<?= $sAreaId ?>">
            <div class="widget-item-wrapper intec-content">
                    <div class="widget-item-wrapper-2 intec-content-wrapper">
                    <?php if ($arVisual['VIEW'] == '1') { ?>
                        <div class="clearfix">
                            <div class="widget-item-picture-wrap" >
                                <?= $vPicture($sPicture, $arResult['VIDEO']) ?>
                            </div>
                            <div class="widget-item-text-wrap">
                                <div class="widget-item-text">
                                    <?php if ($arVisual['TITLE']['SHOW'] && !empty($arResult['TITLE'])) { ?>
                                        <div class="widget-item-title">
                                            <?= $arResult['TITLE'] ?>
                                        </div>
                                    <?php } ?>
                                    <div class="widget-item-name">
                                        <?= $arResult['ITEM']['NAME'] ?>
                                    </div>
                                    <?php if (!empty($arResult['ITEM']['PREVIEW_TEXT'])) { ?>
                                        <div class="widget-item-description">
                                            <?= $arResult['ITEM']['PREVIEW_TEXT'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="widget-item-advantages-wrap">
                                    <?php if (!empty($arResult['ADVANTAGES']) && $arVisual['ADVANTAGES']['SHOW']) { ?>
                                        <? $vAdvantages($arResult['ADVANTAGES']) ?>
                                    <?php } ?>
                                    <?php if ($arVisual['BUTTON_MORE']['SHOW'] && !empty($arResult['LINK'])) { ?>
                                        <? $vButton($arResult['LINK']) ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } else if ($arVisual['VIEW'] == '2') { ?>
                        <div class="intec-grid intec-grid-wrap intec-grid-i-20 intec-grid-a-v-start">
                            <div class="widget-item-picture-wrap intec-grid-item-3 intec-grid-item-800-1" >
                                <?php if ($arVisual['TITLE']['SHOW'] && !empty($arResult['TITLE'])) { ?>
                                    <div class="widget-item-title">
                                        <?= $arResult['TITLE'] ?>
                                    </div>
                                <?php } ?>
                                <div class="widget-item-name">
                                    <?= $arResult['ITEM']['NAME'] ?>
                                </div>
                                <?php if ($arVisual['BUTTON_MORE']['SHOW'] && !empty($arResult['LINK'])) { ?>
                                    <? $vButton($arResult['LINK']) ?>
                                <?php } ?>
                            </div>
                            <div class="widget-item-text-wrap intec-grid-item">
                                <?php if (!empty($arResult['ITEM']['PREVIEW_TEXT'])) { ?>
                                    <div class="widget-item-description">
                                        <?= $arResult['ITEM']['PREVIEW_TEXT'] ?>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($arResult['ADVANTAGES']) && $arVisual['ADVANTAGES']['SHOW']) { ?>
                                    <? $vAdvantages($arResult['ADVANTAGES']) ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="intec-grid intec-grid-wrap intec-grid-i-20 intec-grid-a-v-center">
                            <div class="widget-item-picture-wrap intec-grid-item-4 intec-grid-item-1000-auto intec-grid-item-600-1" >
                                <?= $vPicture($sPicture, $arResult['VIDEO']) ?>
                            </div>
                            <div class="widget-item-text-wrap intec-grid-item">
                                <?php if ($arVisual['TITLE']['SHOW'] && !empty($arResult['TITLE'])) { ?>
                                    <div class="widget-item-title">
                                        <?= $arResult['TITLE'] ?>
                                    </div>
                                <?php } ?>
                                <div class="widget-item-name">
                                    <?= $arResult['ITEM']['NAME'] ?>
                                </div>
                                <?php if (!empty($arResult['ITEM']['PREVIEW_TEXT'])) { ?>
                                    <div class="widget-item-description">
                                        <?= $arResult['ITEM']['PREVIEW_TEXT'] ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['BUTTON_MORE']['SHOW'] && !empty($arResult['LINK'])) { ?>
                                    <? $vButton($arResult['LINK']) ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
         </div>
    </div>
<?= Html::endTag('div'); ?>
<?php include(__DIR__.'/parts/script.php') ?>
