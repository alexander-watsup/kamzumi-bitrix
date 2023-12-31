<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arVisual
 */

?>
<?php return function ($arItems) use (&$arVisual) { ?>
    <?php if (empty($arItems) || !Type::isArray($arItems)) return ?>
    <?php foreach ($arItems as $arItem) {

        $sLink = $arItem['DETAIL_PAGE_URL'];

        $sTag = !empty($sLink) ? 'a' : 'span';
        ?>
        <?= Html::beginTag('div', [
            'class' => [
                'intec-grid-item-1',
                'intec-grid-item-shrink-1',
                'widget-item-child'
            ],
        ]) ?>
            <?= Html::tag($sTag, $arItem['NAME'], [
                'class' => [
                    Html::cssClassFromArray([
                        'widget-item-child-name' => true,
                        'intec-cl-text' => true,
                        'intec-cl-text-light-hover' => $sTag === 'a' ? true : false,
                    ], true)
                ],
                'href' => $sTag === 'a' ? $sLink : null,
                'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
            ]) ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } ?>