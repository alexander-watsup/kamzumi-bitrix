<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
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
//print_r($arResult);
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-store-amount' => [
            '',
            'template-3'
        ]
    ]
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="store-amount-items">
                <?php foreach ($arResult['STORES'] as $arStore) { ?>
                    <div class="store-amount-item-container" data-store-id="<?= $arStore['ID'] ?>">
                        <div class="store-amount-item">
                            <div class="store-amount-item-block">
                                <?= Html::beginTag('div', [
                                    'class' => 'store-amount-item-state',
                                    'data' => [
                                        'role' => 'store.state',
                                        'store-state' => $arStore['AMOUNT_STATUS']
                                    ]
                                ]) ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'intec-grid' => [
                                                '',
                                                'nowrap',
                                                'a-v-center',
                                                'i-h-4'
                                            ]
                                        ]
                                    ]) ?>
                                        <div class="intec-grid-item-auto">
                                            <div class="store-amount-item-state-indicator"></div>
                                        </div>
                                        <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                            <?php if ($arVisual['MIN_AMOUNT']['USE']) { ?>
                                                <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                                                    'class' => [
                                                        'store-amount-item-state-value',
                                                        'store-amount-item-state-colored'
                                                    ]
                                                ]) ?>
                                            <?php } else { ?>
                                                <?= Html::tag('span', Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_2_TEMPLATE_IN_STOCK'), [
                                                    'class' => [
                                                        'store-amount-item-state-value',
                                                        'store-amount-item-state-colored'
                                                    ]
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
                                <?= Html::endTag('div') ?>
                            </div>
                            <div class="store-amount-item-block">
                                <div class="store-amount-item-title">
                                    <?= $arStore['TITLE'] ?>
                                </div>
                            </div>
                            <?php if ($arVisual['PHONE']['SHOW'] && !empty($arStore['PHONE'])) { ?>
                                <div class="store-amount-item-block">
                                    <?= Html::tag('a', $arStore['PHONE']['PRINT'], [
                                        'class' => [
                                            'store-amount-item-contact',
                                            'intec-cl-text-hover'
                                        ],
                                        'href' => 'tel:'.$arStore['PHONE']['HTML']
                                    ]) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['EMAIL']['SHOW'] && !empty($arStore['EMAIL'])) { ?>
                                <div class="store-amount-item-block">
                                    <?= Html::tag('a', $arStore['EMAIL'], [
                                        'class' => [
                                            'store-amount-item-contact',
                                            'intec-cl-text',
                                            'intec-cl-text-light-hover'
                                        ],
                                        'href' => 'mailto:'.$arStore['EMAIL']
                                    ]) ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
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
            })
        </script>
    <?php } ?>
<?= Html::endTag('div') ?>