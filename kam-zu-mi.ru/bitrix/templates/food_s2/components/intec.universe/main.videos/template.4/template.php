<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (empty($arResult['ITEMS']))
    return;

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$sSvg = FileHelper::getFileData(__DIR__.'/svg/item.decoration.svg');

$iCounter = 0;
?>
<div class="widget c-videos c-videos-template-4" id="<?= $sTemplateId ?>">
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
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-items',
                        'intec-grid' => [
                            '',
                            'wrap',
                            'i-h-5',
                            'i-v-12'
                        ]
                    ],
                    'data-role' => 'items'
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = $arItem['PICTURE'];

                        if ($sPicture['SOURCE'] === 'detail' || $sPicture['SOURCE'] === 'preview') {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 500,
                                'height' => 500
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        } else if ($sPicture['SOURCE'] === 'service')
                            $sPicture = $sPicture['SRC'];
                        else
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        $iCounter++;
                    ?>
                        <?php if ($arVisual['LIST_VIEW'] === 'big' && $iCounter < 3) { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-item' => true,
                                    'intec-grid-item' => [
                                        '2' => true,
                                        '500-1' => true
                                    ]
                                ], true)
                            ]) ?>
                                <div class="widget-item-body" id="<?= $sAreaId ?>" data-role="big.elements">
                                    <?= Html::tag('div', $sSvg, [
                                        'class' => 'widget-item-picture',
                                        'title' => $arItem['NAME'],
                                        'data-role' => 'item',
                                        'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                        'data-src' => !empty($arItem['URL']) ? $arItem['URL']['embed'] : null,
                                        'style' => [
                                            'background-image' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] :'url(\''.$sPicture.'\')'
                                        ]
                                    ]) ?>
                                    <?php if ($arVisual['NAME']['SHOW']) { ?>
                                        <div class="widget-item-name">
                                            <?= $arItem['NAME'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } else { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-item' => true,
                                    'intec-grid-item' => [
                                        $arVisual['COLUMNS'] => true,
                                        '1200-3' => $arVisual['COLUMNS'] >= 4,
                                        '768-2' => true,
                                        '500-1' => true
                                    ]
                                ], true)
                            ]) ?>
                            <div class="widget-item-body" id="<?= $sAreaId ?>">
                                <?= Html::tag('div', $sSvg, [
                                    'class' => 'widget-item-picture',
                                    'title' => $arItem['NAME'],
                                    'data-role' => 'item',
                                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                    'data-src' => !empty($arItem['URL']) ? $arItem['URL']['embed'] : null,
                                    'style' => [
                                        'background-image' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] :'url(\''.$sPicture.'\')'
                                    ]
                                ]) ?>
                                <?php if ($arVisual['NAME']['SHOW']) { ?>
                                    <div class="widget-item-name">
                                        <?= $arItem['NAME'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
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
    <?php if (!defined('EDITOR'))
        include(__DIR__.'/parts/script.php');
    ?>
</div>
