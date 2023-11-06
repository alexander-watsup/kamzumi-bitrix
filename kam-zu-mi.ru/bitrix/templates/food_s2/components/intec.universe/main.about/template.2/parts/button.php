<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function ($sLink) use (&$arVisual) { ?>
    <?php $sTag = !empty($sLink) ? 'a' : 'div' ?>
    <div class="widget-button-wrap">
        <?= Html::tag($sTag, Html::stripTags($arVisual['BUTTON_MORE']['TEXT']), array(
            'href' => $sTag === 'a' ? $sLink : null,
            'class' => [
                Html::cssClassFromArray([
                    'widget-item-button' => true,
                    'intec-ui' => [
                        '' => true,
                        'control-button' => true,
                        'mod-round-2' => true,
                        'size-2' => true,
                        'scheme-current' => true,
                        'mod-transparent' => $arVisual['BUTTON_MORE']['VIEW'] == '2' ? true : false,
                    ],
                ], true)
            ]
        )) ?>
    </div>
<?php } ?>