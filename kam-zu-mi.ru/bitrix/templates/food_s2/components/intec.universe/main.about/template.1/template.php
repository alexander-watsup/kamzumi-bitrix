<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
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

$sTag = !empty($arResult['LINK']) ? 'a' : 'div';
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-about',
        'c-about-template-1'
    ],
    'data' => [
        'background' => $arVisual['BACKGROUND']['SHOW'] ? 'true' : 'false'
    ],
    'style' => [
        'background-image' => $arVisual['BACKGROUND']['SHOW'] ? 'url(\''.$arResult['BACKGROUND']['src'].'\')' : null
    ]
]); ?>
    <div class="widget-content">
        <div class="widget-item" id="<?= $sAreaId ?>">
            <?= Html::beginTag('div', [
                'class' => [
                    'widget-item-picture',
                ],
                'data' => [
                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                    'entity' => 'gallery'
                ],
                'style' => [
                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null,
                    'background-size' => $arVisual['PICTURE']['SIZE'],
                    'background-position' => $arVisual['PICTURE']['POSITION']['VERTICAL'].' '.$arVisual['PICTURE']['POSITION']['HORIZONTAL']
                ]
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-decoration',
                        'intec-cl-svg'
                    ],
                    'data-src' => !empty($arResult['VIDEO']) ? $arResult['VIDEO'] : null,
                    'data-play' => !empty($arResult['VIDEO']) ? 'true' : 'false'
                ]); ?>
                    <svg class="widget-item-decoration-icon" viewBox="0 0 122 122" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="intec-cl-svg" d="M121 61C121 94.14 94.14 121 61 121C27.86 121 1 94.14 1 61C1 27.86 27.86 1 61 1C94.14 1 121 27.86 121 61Z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M53.9405 41.3864L79.8205 56.6931C83.0938 58.6264 83.0938 63.3664 79.8205 65.2997L53.9405 80.6064C50.6071 82.5797 46.3938 80.1731 46.3938 76.2997V45.6931C46.3938 41.8197 50.6071 39.4131 53.9405 41.3864Z" fill="white" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
            <div class="widget-item-wrapper intec-content">
                <div class="widget-item-wrapper-2 intec-content-wrapper">
                    <div class="intec-grid intec-grid-wrap intec-grid-i-20 intec-grid-a-v-center">
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
                            <?php if ($arVisual['BUTTON']['SHOW'] && !empty($arResult['LINK'])) { ?>
                                <div class="widget-button-wrap">
                                    <?= Html::tag($sTag, Html::stripTags($arVisual['BUTTON']['TEXT']), array(
                                        'href' => $sTag === 'a' ? $arResult['LINK'] : null,
                                        'class' => array(
                                            'widget-item-button',
                                            'intec-ui' => array(
                                                '',
                                                'control-button',
                                                'mod-round-2',
                                                'size-2',
                                                'scheme-current'
                                            )
                                        )
                                    )) ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="widget-item-column-right intec-grid-item-2"></div>
                    </div>
                </div>
            </div>
         </div>
    </div>
<?= Html::endTag('div'); ?>
<?php include(__DIR__.'/parts/script.php') ?>
