<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<?php return function () { ?>
    <div class="catalog-element-offers-list-item-counter" data-role="counter">
        <?= Html::tag('a', '-', [
            'class' => 'catalog-element-offers-list-item-counter-button',
            'href' => 'javascript:void(0)',
            'data-type' => 'button',
            'data-action' => 'decrement'
        ]) ?>
        <?= Html::input('text', null, 0, [
            'data-type' => 'input',
            'class' => 'catalog-element-offers-list-item-counter-input'
        ]) ?>
        <?= Html::tag('a', '+', [
            'class' => 'catalog-element-offers-list-item-counter-button',
            'href' => 'javascript:void(0)',
            'data-type' => 'button',
            'data-action' => 'increment'
        ]) ?>
    </div>
<?php } ?>