<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$arForm = $arResult['FORM'];
$arForm['PARAMETERS'] = [
    'id' => $arForm['ID'],
    'template' => $arForm['TEMPLATE'],
    'parameters' => [
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ORDER',
        'CONSENT_URL' => $arForm['CONSENT']
    ],
    'settings' => [
        'title' => $arForm['TITLE']
    ],
    'fields' => [
        $arForm['FIELD'] => null
    ]
];

if (empty($arForm['BUTTON']))
    $arForm['BUTTON'] = Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_22_ORDER_BUTTON_DEFAULT');

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';
?>
<div class="widget c-services c-services-template-22" id="<?= $sTemplateId ?>">
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
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-items',
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-v-stretch',
                            'i-15'
                        ]
                    ],
                    'data' => [
                        'order' => $arForm['USE'] ? 'true' : 'false'
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

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet(
                                $sPicture, [
                                'width' => 60,
                                'height' => 60
                            ],
                                BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                            );

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        $arData = $arItem['DATA'];
                        $arForm['PARAMETERS']['fields'][$arForm['FIELD']] = $arItem['NAME'];
                    ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-item',
                                'intec-grid-item-2',
                                'intec-grid-item-650-1'
                            ],
                            'id' => $sAreaId,
                            'data' => [
                                'price' => $arVisual['PRICE']['SHOW'] && !empty($arData['PRICE']) ? 'true' : 'false'
                            ]
                        ]) ?>
                            <div class="widget-item-wrapper">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center">
                                    <div class="intec-grid-item-auto widget-item-picture-wrap">
                                        <?= Html::beginTag('div', [
                                            'class' => 'widget-item-picture',
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                            ]
                                        ]) ?>
                                            <?= Html::img($sPicture, [
                                                'alt' => $arItem['NAME'],
                                                'title' => $arItem['NAME'],
                                            ]) ?>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                    <div class="intec-grid-item">
                                        <?= Html::tag($sTag, $arItem['NAME'], [
                                            'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                            'class' => Html::cssClassFromArray([
                                                'widget-item-name' => true,
                                                'intec-cl-text-hover' => $arVisual['LINK']['USE']
                                            ], true)
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="widget-item-information">
                                    <?php if ($arVisual['PREVIEW']['SHOW'] && !empty($arItem['PREVIEW_TEXT'])) { ?>
                                        <div class="widget-item-description">
                                            <?= $arItem['PREVIEW_TEXT'] ?>
                                        </div>
                                    <?php } ?>
                                    <div class="widget-item-price-wrap">
                                        <div class="intec-grid intec-grid-wrap intec-grid-i-10 intec-grid-a-v-center">
                                            <?php if ($arVisual['PRICE']['SHOW'] && !empty($arData['PRICE'])) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-768-1 intec-grid-item-650-auto intec-grid-item-500-1">
                                                    <div class="widget-item-price">
                                                        <?= $arData['PRICE'] ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($arForm['USE']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-768-1 intec-grid-item-650-auto intec-grid-item-500-1">
                                                    <?= Html::tag('div', Html::stripTags($arForm['BUTTON']), [
                                                        'class' => [
                                                            'widget-item-button',
                                                            'intec-ui' => [
                                                                '',
                                                                'control-button',
                                                                'mod-round-2',
                                                                'size-5',
                                                                'scheme-current'
                                                            ]
                                                        ],
                                                        'onclick' => '(function() {
                                                    universe.forms.show('.JavaScript::toObject($arForm['PARAMETERS']).');
                                    
                                                    if (window.yandex && window.yandex.metrika) {
                                                        window.yandex.metrika.reachGoal(\'forms.open\');
                                                        window.yandex.metrika.reachGoal('.JavaScript::toObject('forms.'.$arForm['PARAMETERS']['id'].'.open').');
                                                    }
                                                })()'
                                                    ]) ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
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
</div>