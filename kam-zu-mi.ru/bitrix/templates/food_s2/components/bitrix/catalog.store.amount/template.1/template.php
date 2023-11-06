<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 */

$this->setFrameMode(true);

if (empty($arResult['STORES']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<?php if (!empty($arResult['STORES'])) { ?>
    <div class="ns-bitrix c-catalog-store-amount c-catalog-store-amount-template-1" id="<?= $sTemplateId ?>">
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
                                                ],
                                                'data-role' => 'store.quantity'
                                            ]) ?>
                                        <?php } else { ?>
                                            <?= Html::tag('span', Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_1_TEMPLATE_IN_STOCK'), [
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
                    </div>
                </div>
                <?/*<div class="catalog-store-amount-item" data-store-id="<?= $arStore['ID'] ?>">
                    <div class="intec-grid intec-grid-a-v-start intec-grid-i-h-4">
                        <div class="intec-grid-item-auto">
                            <?= Html::tag('div', null, [
                                'class' => 'catalog-store-amount-indicator',
                                'data' => [
                                    'role' => 'store.state',
                                    'store-state' => $arStore['AMOUNT_STATUS']
                                ]
                            ]) ?>
                        </div>
                        <div class="intec-grid-item">
                            <?= Html::tag('div', $arStore['TITLE'], [
                                'class' => 'catalog-store-amount-name'
                            ]) ?>
                            <div class="catalog-store-amount-quantity">
                                <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                                    'data-role' => 'store.quantity'
                                ]) ?>
                                <?php if (!$arVisual['MIN_AMOUNT']['USE']) { ?>
                                    <?= Html::tag('span', !$arResult['IS_SKU'] ? ArrayHelper::getFirstValue($arResult['MEASURES']) : null, [
                                        'data-role' => 'store.measure'
                                    ]) ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>*/?>
            <?php } ?>
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
<?php } ?>
