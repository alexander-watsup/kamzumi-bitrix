<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>

<div class="basket-coupon-field">
    <div class="basket-coupon-field-name">
        <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_COUPON_FIELD_NAME') ?>
    </div>
    <div class="basket-coupon-field-description">
        <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_COUPON_FIELD_DESCRIPTION') ?>
    </div>
    <div class="basket-coupon-field-input">
        <?= Html::input('text', null, null, [
            'class' => [
                'intec-ui' => [
                    '',
                    'control-input',
                    'view-1',
                    'size-1',
                    'mod-block'
                ]
            ],
            'placeholder' => Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_COUPON_FIELD_PLACEHOLDER'),
            'data-entity' => 'basket-coupon-input'
        ]) ?>
    </div>
</div>