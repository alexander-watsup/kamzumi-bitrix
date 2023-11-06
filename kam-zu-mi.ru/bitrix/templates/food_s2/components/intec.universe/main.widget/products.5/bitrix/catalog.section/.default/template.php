<?php if (defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

Loc::loadMessages(__FILE__);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
$arBlocks = $arResult['BLOCKS'];

$bActionButtonsShow = $arResult['DELAY']['USE'] ||
    $arResult['COMPARE']['USE'] ||
    $arResult['DELAY']['SHOW_INACTIVE'] ||
    $arResult['COMPARE']['SHOW_INACTIVE'];

/**
 * @var Closure $dData(&$arItem)
 * @var Closure $vButtons(&$arItem)
 * @var Closure $vImage(&$arItem)
 * @var Closure $vPrice(&$arItem)
 * @var Closure $vPurchase(&$arItem)
 * @var Closure $vQuantity(&$arItem)
 * @var Closure $vSku($arProperties)
 */
include(__DIR__.'/parts/data.php');
include(__DIR__.'/parts/image.php');
include(__DIR__.'/parts/price.php');
include(__DIR__.'/parts/price.total.php');
include(__DIR__.'/parts/measure.php');
include(__DIR__.'/parts/action.buttons.php');
include(__DIR__.'/parts/order.buttons.php');
include(__DIR__.'/parts/quantity.php');
include(__DIR__.'/parts/sku.php');
include(__DIR__.'/parts/purchase.php');

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-widget',
        'c-widget-products-5'
    ],
    'data' => [
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : ''
    ]
]) ?>
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="<?= Html::cssClassFromArray([
                            'widget-title',
                            'align-'.$arBlocks['HEADER']['ALIGN']
                        ]) ?>">
                            <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="<?= Html::cssClassFromArray([
                            'widget-description',
                            'align-'.$arBlocks['DESCRIPTION']['ALIGN']
                        ]) ?>">
                            <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?php if ($arResult['MODE'] === 'all' || $arResult['MODE'] === 'categories') { ?>
                    <?php if ($arVisual['VIEW'] === 'tabs') { ?>
                        <ul class="<?= Html::cssClassFromArray([
                            'widget-tabs',
                            'intec-ui' => [
                                '',
                                'control-tabs',
                                'mod-block',
                                'mod-position-'.$arVisual['TABS']['ALIGN'],
                                'scheme-current',
                                'view-1'
                            ]
                        ]) ?>">
                            <?php $iCounter = 0 ?>
                            <?php foreach ($arResult['CATEGORIES'] as $arCategory) { ?>
                                <li class="<?= Html::cssClassFromArray([
                                    'intec-ui-part-tab' => true,
                                    'active' => $iCounter === 0
                                ], true) ?>">
                                    <a href="<?= '#'.$sTemplateId.'-tab-'.$iCounter ?>" role="tab" data-toggle="tab"><?= $arCategory['NAME'] ?></a>
                                </li>
                                <?php $iCounter++ ?>
                            <?php } ?>
                        </ul>
                        <div class="widget-tabs-content intec-ui intec-ui-control-tabs-content">
                            <?php $iCounter = 0 ?>
                            <?php foreach ($arResult['CATEGORIES'] as $arCategory) { ?>
                                <?= Html::beginTag('div', [
                                    'id' => $sTemplateId.'-tab-'.$iCounter,
                                    'class' => Html::cssClassFromArray([
                                        'intec-ui-part-tab' => true,
                                        'active' => $iCounter === 0
                                    ], true),
                                    'role' => 'tabpanel'
                                ]) ?>
                                    <?php $arProperties = &$arCategory['PROPERTIES'] ?>
                                    <?php $arItems = &$arCategory['ITEMS'] ?>
                                    <?php include(__DIR__.'/parts/items.php') ?>
                                <?= Html::endTag('div') ?>
                                <?php $iCounter++ ?>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="widget-sections">
                            <?php foreach ($arResult['CATEGORIES'] as $arCategory) { ?>
                                <div class="widget-section">
                                    <?php if ($arVisual['SECTIONS']['TITLE']['SHOW']) { ?>
                                        <?= Html::tag('div', $arCategory['NAME'], [
                                            'class' => [
                                                'widget-section-name',
                                                'intec-template-part',
                                                'intec-template-part-title'
                                            ],
                                            'data' => [
                                                'align' => $arVisual['SECTIONS']['TITLE']['ALIGN']
                                            ]
                                        ]) ?>
                                    <?php } ?>
                                    <div class="widget-section-content">
                                        <?php $arProperties = &$arCategory['PROPERTIES'] ?>
                                        <?php $arItems = &$arCategory['ITEMS'] ?>
                                        <?php include(__DIR__.'/parts/items.php') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } else if ($arResult['MODE'] === 'category') { ?>
                    <?php $arCategory = null ?>
                    <?php $arProperties = &$arResult['PROPERTIES'] ?>
                    <?php $arItems = &$arResult['ITEMS'] ?>
                    <?php include(__DIR__.'/parts/items.php') ?>
                <?php } ?>

                <?php if (!defined('EDITOR')) include(__DIR__.'/parts/script.php') ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div');?>
