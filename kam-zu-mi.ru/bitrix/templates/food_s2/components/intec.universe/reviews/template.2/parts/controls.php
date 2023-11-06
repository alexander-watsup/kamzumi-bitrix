<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arSvg
 */

?>
<div class="widget-controls">
    <div class="widget-controls-content">
        <div class="widget-controls-item">
            <?php if ($arVisual['POST']['ALLOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-controls-button',
                        'widget-controls-button-active',
                        'intec-cl-border',
                        'intec-cl-background-hover'
                    ],
                    'data-role' => 'post.toggle'
                ]) ?>
                    <?= Html::tag('div', $arSvg['CONTROLS']['BUTTON'], [
                        'class' => [
                            'widget-controls-button-icon',
                            'widget-controls-button-part',
                            'intec-cl-svg-path-stroke'
                        ]
                    ]) ?>
                    <?= Html::tag('div', Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_CONTROLS_BUTTON_POST'), [
                        'class' => [
                            'widget-controls-button-text',
                            'widget-controls-button-part',
                            'intec-cl-text'
                        ]
                    ]) ?>
                <?= Html::endTag('div') ?>
            <?php } else { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-controls-button',
                        'widget-controls-button-disabled'
                    ],
                    'title' => Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_CONTROLS_BUTTON_POST_DISABLED')
                ]) ?>
                    <?= Html::tag('div', $arSvg['CONTROLS']['BUTTON'], [
                        'class' => [
                            'widget-controls-button-icon',
                            'widget-controls-button-part'
                        ]
                    ]) ?>
                    <?= Html::tag('div', Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_CONTROLS_BUTTON_POST'), [
                        'class' => [
                            'widget-controls-button-text',
                            'widget-controls-button-part'
                        ]
                    ]) ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
    </div>
</div>