<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>

<div class="basket-item-image">
    {{#DETAIL_PAGE_URL}}
        <?= Html::beginTag('a', [
            'class' => 'basket-item-image-link',
            'href' => '{{DETAIL_PAGE_URL}}'
        ]) ?>
    {{/DETAIL_PAGE_URL}}
        <?= Html::img('{{{IMAGE_URL}}}{{^IMAGE_URL}}'.SITE_TEMPLATE_PATH.'/images/picture.missing.png{{/IMAGE_URL}}', [
            'class' => 'intec-image-effect'
        ]) ?>
    {{#DETAIL_PAGE_URL}}
        <?= Html::endTag('a') ?>
    {{/DETAIL_PAGE_URL}}
</div>