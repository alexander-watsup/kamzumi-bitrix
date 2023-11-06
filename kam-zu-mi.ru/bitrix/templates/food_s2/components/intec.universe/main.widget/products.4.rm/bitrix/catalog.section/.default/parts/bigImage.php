<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php $vBigImage = function (&$arItem) use (&$arResult, &$arVisual) {
    $sPicture = $arItem['BANNER']['PICTURE'];

    if (empty($sPicture) && !empty($arItem['PICTURES']))
        $sPicture = reset($arItem['PICTURES']);

    if (!empty($sPicture)) {
        $sPicture = CFile::ResizeImageGet($sPicture, [
            'width' => 700,
            'height' => 700
        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

        if (!empty($sPicture))
            $sPicture = $sPicture['src'];
    }

    if (empty($sPicture))
        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

?>
    <?= Html::beginTag('div', [
        'class' => [
            'widget-item-big-image',
            'intec-image'
        ]
    ]) ?>
        <?= Html::tag('div', '', [
            'class' => [
                'widget-item-big-image-wrapper',
                'intec-image-effect'
            ],
            'data' => [
                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
            ],
            'style' => [
                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
            ]
        ]) ?>
    <?= Html::endTag('div') ?>
<?php } ?>