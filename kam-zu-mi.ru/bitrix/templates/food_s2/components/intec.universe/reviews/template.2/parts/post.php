<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var CMain $APPLICATION
 */

$sCaptchaCode = htmlspecialchars($APPLICATION->CaptchaGetCode());

$arPost = [
    'NAME' => $bError ? $_REQUEST['NAME'] : null,
    'PREVIEW_TEXT' => $bError ? $_REQUEST['PREVIEW_TEXT'] : null
];

?>
<?= Html::beginTag('div', [
    'class' => [
        'widget-form',
        'intec-ui-form'
    ],
    'data' => [
        'role' => 'post.content',
        'expanded' => $bError ? 'true' : 'false'
    ]
]) ?>
    <?= Html::beginForm($APPLICATION->GetCurPageParam()) ?>
        <div class="widget-form-fields intec-ui-form-fields">
            <label class="intec-ui-form-field intec-ui-form-field-required">
                <span class="intec-ui-form-field-title">
                    <?= Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_FORM_INPUT_NAME') ?>
                </span>
                <span class="intec-ui-form-field-content">
                    <?= Html::input('text', 'NAME', $arPost['NAME'], [
                        'class' => [
                            'intec-ui' => [
                                '',
                                'control-input',
                                'mod-block',
                                'mod-round-2',
                                'size-2'
                            ]
                        ]
                    ]) ?>
                </span>
            </label>
            <label class="intec-ui-form-field intec-ui-form-field-required">
                <span class="intec-ui-form-field-title">
                    <?= Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_FORM_INPUT_REVIEW') ?>
                </span>
                <span class="intec-ui-form-field-content">
                    <?= Html::textarea('PREVIEW_TEXT', $arPost['PREVIEW_TEXT'], [
                        'class' => [
                            'intec-ui' => [
                                '',
                                'control-input',
                                'mod-block',
                                'mod-round-2',
                                'size-2'
                            ]
                        ]
                    ]) ?>
                </span>
            </label>
            <?php if ($arVisual['CAPTCHA']['USE']) { ?>
                <?= Html::hiddenInput('CAPTCHA_SID', $sCaptchaCode) ?>
                <label class="widget-form-captcha intec-ui-form-field intec-ui-form-field-required">
                    <span class="intec-ui-form-field-title">
                        <?= Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_FORM_INPUT_CAPTCHA') ?>
                    </span>
                    <span class="widget-form-captcha-field intec-ui-form-field-content">
                        <div class="widget-form-captcha-picture">
                            <?= Html::img('/bitrix/tools/captcha.php?captcha_sid='.$sCaptchaCode) ?>
                        </div>
                        <?= Html::input('text', 'CAPTCHA', null, [
                            'class' => [
                                'intec-ui' => [
                                    '',
                                    'control-input',
                                    'mod-block',
                                    'mod-round-2',
                                    'size-2'
                                ]
                            ]
                        ]) ?>
                    </span>
                </label>
            <?php } ?>
        </div>
        <?php if ($arVisual['CONSENT']['SHOW']) { ?>
            <div class="widget-form-consent">
                <div class="widget-form-consent-content">
                    <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                        <?= Html::input('checkbox', null, null, [
                            'checked' => 'checked',
                            'onchange' => 'this.checked = !this.checked'
                        ]) ?>
                        <span class="intec-ui-part-selector"></span>
                        <span class="intec-ui-part-content">
                            <?php if (!empty($arVisual['CONSENT']['URL'])) { ?>
                                <?= Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_FORM_CONSENT_URL_PART_1') ?>
                                <?= Html::tag('a', Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_FORM_CONSENT_URL_PART_2'), [
                                    'href' => $arVisual['CONSENT']['URL'],
                                    'target' => '_blank'
                                ]) ?>
                            <?php } else { ?>
                                <?= Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_FORM_CONSENT_URL_EMPTY') ?>
                            <?php } ?>
                        </span>
                    </label>
                </div>
            </div>
        <?php } ?>
        <div class="widget-form-buttons">
            <div class="widget-form-buttons-content">
                <?= Html::button(Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_FORM_INPUT_SUBMIT'), [
                    'class' => [
                        'widget-form-button',
                        'intec-cl-text',
                        'intec-cl-border',
                        'intec-cl-background-hover'
                    ],
                    'type' => 'submit',
                    'name' => 'submit',
                    'value' => 'send'
                ]) ?>
            </div>
        </div>
    <?= Html::endForm() ?>
<?= Html::endTag('div') ?>