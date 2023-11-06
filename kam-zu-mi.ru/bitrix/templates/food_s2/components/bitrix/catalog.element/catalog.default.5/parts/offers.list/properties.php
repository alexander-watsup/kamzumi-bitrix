<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

?>
<?php return function (&$arOffer) use (&$arResult) { ?>
    <div class="catalog-element-offers-list-item-properties">
        <?php foreach ($arResult['SKU_PROPS'] as $arProperty) {

            $sPropertyValue = ArrayHelper::getValue($arProperty, [
                'values',
                $arOffer['TREE']['PROP_'.$arProperty['id']],
                'name'
            ]);

            if (empty($sPropertyValue))
                continue;

        ?>
            <div class="catalog-element-offers-list-item-properties-item">
                <?= Html::tag('div', $arProperty['name'], [
                    'class' => [
                        'catalog-element-offers-list-item-properties-item-name',
                        'catalog-element-offers-list-item-properties-item-part'
                    ]
                ]) ?>
                <?= Html::tag('div', $sPropertyValue, [
                    'class' => [
                        'catalog-element-offers-list-item-properties-item-value',
                        'catalog-element-offers-list-item-properties-item-part'
                    ]
                ]) ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>