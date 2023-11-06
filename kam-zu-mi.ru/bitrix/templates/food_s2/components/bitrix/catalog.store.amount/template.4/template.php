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
                <div class="store-amount-item" data-store-id="<?= $arStore['ID'] ?>">
                    <div class="store-amount-item-container">
                        <div class="store-amount-item-block">
                            <?= Html::beginTag('div', [
                                'class' => 'store-amount-item-state',
                                'data' => [
                                    'role' => 'store.state',
                                    'store-state' => $arStore['AMOUNT_STATUS']
                                ]
                            ]) ?>
                                <div class="store-amount-item-state-content">
                                    <div class="store-amount-item-state-indicator store-amount-item-state-part"></div>
                                    <div class="store-amount-item-state-text store-amount-item-state-part">
                                        <?php if ($arVisual['MIN_AMOUNT']['USE']) { ?>
                                            <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                                                'class' => [
                                                    'store-amount-item-state-value',
                                                    'store-amount-item-state-colored'
                                                ],
                                                'data-role' => 'store.quantity'
                                            ]) ?>
                                        <?php } else { ?>
                                            <?= Html::tag('span', Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_4_TEMPLATE_IN_STOCK'), [
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
                                </div>
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
                                    'title' => $arStore['PHONE']['PRINT'],
                                    'href' => 'tel:'.$arStore['PHONE']['HTML']
                                ]) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['EMAIL']['SHOW'] && !empty($arStore['EMAIL'])) { ?>
                            <div class="store-amount-item-block">
                                <?= Html::tag('a', $arStore['EMAIL'], [
                                    'class' => [
                                        'store-amount-item-contact',
                                        'intec-cl-text-hover'
                                    ],
                                    'title' => $arStore['EMAIL'],
                                    'href' => 'mailto:'.$arStore['EMAIL']
                                ]) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['SCHEDULE']['SHOW'] && !empty($arStore['SCHEDULE'])) { ?>
                            <div class="store-amount-item-block">
                                <div class="store-amount-item-title">
                                    <?= $arStore['SCHEDULE'] ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($arResult['IS_SKU'] && empty($arParams['OFFER_ID'])) { ?>
        <script type="text/javascript">
            $(document).on('ready', function () {
                let storeOffers = new intecCatalogStoreOffers(
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
