<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function ($sPicture, $sVideo) use (&$arVisual) { ?>
    <?= Html::beginTag('div', [
        'class' => [
            'widget-item-picture',
        ],
        'data' => [
            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
            'entity' => 'gallery'
        ],
        'style' => [
            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null,
            'background-size' => $arVisual['PICTURE']['SIZE'],
            'background-position' => $arVisual['PICTURE']['POSITION']['VERTICAL'].' '.$arVisual['PICTURE']['POSITION']['HORIZONTAL']
        ]
    ]) ?>
        <?php if (!empty($sVideo)) { ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'widget-item-decoration',
                    'intec-cl-svg',
                    'intec-cl-svg-hover'
                ],
                'data-src' => !empty($sVideo) ? $sVideo : null,
                'data-play' => !empty($sVideo) ? 'true' : 'false'
            ]); ?>
                <span class="intec-aligner"></span>
                <span class="widget-item-decoration-icon-wrap">
                    <svg class="widget-item-decoration-icon" viewBox="0 0 122 122" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="intec-cl-svg intec-cl-svg-hover" d="M121 61C121 94.14 94.14 121 61 121C27.86 121 1 94.14 1 61C1 27.86 27.86 1 61 1C94.14 1 121 27.86 121 61Z" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M53.9405 41.3864L79.8205 56.6931C83.0938 58.6264 83.0938 63.3664 79.8205 65.2997L53.9405 80.6064C50.6071 82.5797 46.3938 80.1731 46.3938 76.2997V45.6931C46.3938 41.8197 50.6071 39.4131 53.9405 41.3864Z" fill="white" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>
