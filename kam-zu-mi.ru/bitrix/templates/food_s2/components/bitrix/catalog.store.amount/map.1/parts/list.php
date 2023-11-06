<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var Closure $vQuantity(&$arStore)
 */

?>
<div class="store-amount-list-content scrollbar-outer" data-scroll>
    <?php foreach ($arResult['STORES'] as $arStore) { ?>
        <?= Html::beginTag('div', [
            'class' => 'store-amount-list-item',
            'data-role' => 'store.list.item',
            'data-active' => 'false',
            'data-store-id' => $arStore['ID']
        ]) ?>
            <div class="store-amount-list-item-name">
                <?= $arStore['TITLE'] ?>
            </div>
            <div class="store-amount-list-item-quantity">
                <?php $vQuantity($arStore) ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
</div>
