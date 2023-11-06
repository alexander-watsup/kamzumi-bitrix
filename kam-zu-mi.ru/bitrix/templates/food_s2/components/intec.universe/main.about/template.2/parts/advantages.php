<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function ($arData) use (&$arVisual) { ?>
    <div class="widget-item-advantages">
        <div class="intec-grid intec-grid-wrap intec-grid-i-13">
            <?php foreach ($arData as $arItem) {
                $arImage = [
                    'TYPE' => 'picture',
                    'SOURCE' => null
                ];

                if (!empty($arItem['IMAGE'])) {
                    if ($arItem['IMAGE']['CONTENT_TYPE'] === 'image/svg+xml') {
                        $arImage['TYPE'] = 'svg';
                        $arImage['SOURCE'] = $arItem['IMAGE']['SRC'];
                    } else {
                        $arImage['SOURCE'] = CFile::ResizeImageGet($arItem['IMAGE'], array(
                            'width' => 48,
                            'height' => 48
                        ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                        if (!empty($arImage['SOURCE'])) {
                            $arImage['SOURCE'] = $arImage['SOURCE']['src'];
                        } else {
                            $arImage['SOURCE'] = null;
                        }
                    }
                }

                if (empty($arImage['SOURCE'])) {
                    $arImage['TYPE'] = 'picture';
                    $arImage['SOURCE'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                }
                ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        Html::cssClassFromArray([
                            'intec-grid-item' => [
                                $arVisual['ADVANTAGES']['COLUMNS'] => true,
                                '600-1' => true,
                                '1000-2' => $arVisual['ADVANTAGES']['COLUMNS'] >= 3
                            ],
                        ], true)
                    ]
                ]) ?>
                    <div class="intec-grid intec-grid-i-8">
                        <div class="intec-grid-item-auto">
                            <?php if ($arImage['TYPE'] === 'svg') { ?>
                                <?= Html::tag('div', FileHelper::getFileData('@root/'.$arImage['SOURCE']), [
                                    'class' => [
                                        'widget-item-advantages-picture',
                                        'intec-cl-svg',
                                        'intec-image-effect'
                                    ]
                                ]) ?>
                            <?php } else { ?>
                                <?= Html::tag('div', null, [
                                    'class' => [
                                        'widget-item-advantages-picture',
                                        'intec-image-effect'
                                    ],
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arImage['SOURCE'] : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arImage['SOURCE'].'\')' : null
                                    ]
                                ]) ?>
                            <?php } ?>
                        </div>
                        <div class="intec-grid-item">
                            <div class="widget-item-advantages-name">
                                <?= $arItem['NAME'] ?>
                            </div>
                            <div class="widget-item-advantages-description">
                                <?= $arItem['PREVIEW_TEXT'] ?>
                            </div>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
    </div>
<?php } ?>
