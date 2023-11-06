<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['STORES']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'PHONE' => FileHelper::getFileData(__DIR__.'/svg/contact.phone.svg'),
    'EMAIL' => FileHelper::getFileData(__DIR__.'/svg/contacn.email.svg')
];
//print_r($arResult);
?>
<div class="ns-bitrix c-catalog-store-amount c-catalog-store-amount-template-2" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="store-amount-items">
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch">
                    <?php foreach ($arResult['STORES'] as $arStore) {

                        $sPicture = $arStore['PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 500,
                                'height' => 500
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'store-amount-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1024-3' => $arVisual['COLUMNS'] >= 4,
                                    '768-2' => true,
                                    '500-1' => true
                                ]
                            ], true),
                            'data-store-id' => $arStore['ID']
                        ]) ?>
                            <div class="store-amount-item-content">
                                <?php if ($arVisual['PICTURE']['SHOW'] && !empty($arStore['PICTURE'])) { ?>
                                    <?= Html::tag('div', null, [
                                        'class' => [
                                            'store-amount-item-picture',
                                            'intec-image-effect'
                                        ],
                                        'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                        'style' => [
                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                        ]
                                    ]) ?>
                                <?php } ?>
                                <div class="store-amount-item-state-container store-amount-item-block">
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'store-amount-item-state',
                                            'intec-grid' => [
                                                '',
                                                'a-v-center',
                                                'i-h-4'
                                            ]
                                        ],
                                        'data' => [
                                            'role' => 'store.state',
                                            'store-state' => $arStore['AMOUNT_STATUS']
                                        ]
                                    ]) ?>
                                        <div class="intec-grid-item-auto">
                                            <?= Html::tag('div', null, [
                                                'class' => 'store-amount-item-state-indicator',
                                            ]) ?>
                                        </div>
                                        <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                            <?php if ($arVisual['MIN_AMOUNT']['USE']) { ?>
                                                <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                                                    'class' => [
                                                        'store-amount-item-state-value',
                                                        'store-amount-item-state-colored'
                                                    ],
                                                    'data-role' => 'store.quantity'
                                                ]) ?>
                                            <?php } else { ?>
                                                <?= Html::tag('span', Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_2_TEMPLATE_IN_STOCK'), [
                                                    'class' => [
                                                        'store-amount-item-state-value',
                                                        'store-amount-item-state-colored'
                                                    ],
                                                ]) ?>
                                                <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                                                    'class' => 'store-amount-item-state-value',
                                                    'data-role' => 'store.quantity'
                                                ]) ?>
                                                <?php if (empty($arParams['OFFER_ID'])) { ?>
                                                    <?= Html::tag('span', ArrayHelper::getFirstValue($arResult['MEASURES']), [
                                                        'class' => 'store-amount-item-state-value',
                                                        'data-role' => 'store.measure'
                                                    ]) ?>
                                                <?php } else { ?>
                                                    <?= Html::tag('span', ArrayHelper::getValue($arResult, ['MEASURES', $arParams['OFFER_ID']]), [
                                                        'class' => 'store-amount-item-state-value',
                                                        'data-role' => 'store.measure'
                                                    ]) ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                </div>
                                <?= Html::tag('div', $arStore['TITLE'], [
                                    'class' => [
                                        'store-amount-item-title',
                                        'store-amount-item-block'
                                    ],
                                    'title' => $arStore['TITLE']
                                ]) ?>
                                <?php if ($arVisual['SCHEDULE']['SHOW'] && !empty($arStore['SCHEDULE'])) { ?>
                                    <?= Html::tag('div', $arStore['SCHEDULE'], [
                                        'class' => [
                                            'store-amount-item-schedule',
                                            'store-amount-item-block'
                                        ]
                                    ]) ?>
                                <?php } ?>
                                <?php if ($arVisual['PHONE']['SHOW'] && !empty($arStore['PHONE'])) { ?>
                                    <div class="store-amount-item-contact-container store-amount-item-block">
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'store-amount-item-contact',
                                                'intec-grid' => [
                                                    '',
                                                    'a-v-center',
                                                    'i-h-4'
                                                ]
                                            ]
                                        ]) ?>
                                            <?= Html::tag('div', $arSvg['PHONE'], [
                                                'class' => [
                                                    'intec-grid-item-auto',
                                                    'store-amount-item-contact-icon'
                                                ]
                                            ]) ?>
                                            <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                <?= Html::tag('a', $arStore['PHONE']['PRINT'], [
                                                    'class' => [
                                                        'store-amount-item-contact-value',
                                                        'intec-cl-text-hover'
                                                    ],
                                                    'href' => 'tel:'.$arStore['PHONE']['HTML'],
                                                    'title' => $arStore['PHONE']['PRINT'],
                                                    'data-view' => 'bold'
                                                ]) ?>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['EMAIL']['SHOW'] && !empty($arStore['EMAIL'])) { ?>
                                    <div class="store-amount-item-contact-container store-amount-item-block">
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'store-amount-item-contact',
                                                'intec-grid' => [
                                                    '',
                                                    'a-v-center',
                                                    'i-h-4'
                                                ]
                                            ]
                                        ]) ?>
                                        <?= Html::tag('div', $arSvg['EMAIL'], [
                                            'class' => [
                                                'intec-grid-item-auto',
                                                'store-amount-item-contact-icon'
                                            ]
                                        ]) ?>
                                        <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                            <?= Html::tag('a', $arStore['EMAIL'], [
                                                'class' => [
                                                    'store-amount-item-contact-value',
                                                    'intec-cl-text',
                                                    'intec-cl-text-light-hover'
                                                ],
                                                'href' => 'mailto:'.$arStore['EMAIL'],
                                                'title' => $arStore['EMAIL'],
                                                'data-view' => 'normal'
                                            ]) ?>
                                        </div>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arStore['DESCRIPTION'])) { ?>
                                    <?= Html::tag('div', $arStore['DESCRIPTION'], [
                                        'class' => [
                                            'store-amount-item-description',
                                            'store-amount-item-block'
                                        ],
                                        'title' => $arStore['DESCRIPTION']
                                    ]) ?>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php if ($arResult['IS_SKU'] && empty($arParams['OFFER_ID'])) { ?>
        <script type="text/javascript">
            $(document).on('ready', function () {
                var storeOffers = new intecCatalogStoreOffers(
                    <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                    <?= JavaScript::toObject($arResult['JS']) ?>
                );

                offers.on('change', function (event, offer, values) {
                    storeOffers.offerOnChange(offer.id);
                });

                storeOffers.offerOnChange(offers.getCurrent().id);
            });
        </script>
    <?php } ?>
</div>