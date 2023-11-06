<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var string $sTag
 */

?>
<?php $vPicture = function (&$arItem) use (&$arVisual, &$sTag) { ?>
    <?php if ($arVisual['VIDEO']['SHOW'] && !empty($arItem['DATA']['VIDEO'])) { ?>
        <?= Html::beginTag('div', [
            'class' => [
                'widget-item-picture',
                'intec-cl-svg-path-stroke',
                'intec-cl-svg-path-fill'
            ],
            'style' => [
                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arItem['DATA']['VIDEO'][$arVisual['VIDEO']['QUALITY']].'\')' : null
            ],
            'data' => [
                'role' => 'video',
                'src' => $arItem['DATA']['VIDEO']['iframe'],
                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                'original' => $arVisual['LAZYLOAD']['USE'] ? $arItem['DATA']['VIDEO'][$arVisual['VIDEO']['QUALITY']] : null
            ]
        ]) ?>
            <div class="intec-aligner"></div>
            <svg class="widget-item-picture-icon" width="98" height="98" viewBox="0 0 98 98" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M97 49C97 75.512 75.512 97 49 97C22.488 97 1 75.512 1 49C1 22.488 22.488 1 49 1C75.512 1 97 22.488 97 49Z" fill="#0065FF" stroke="#0065FF" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                <path class="widget-item-picture-icon-path-white" d="M43.3524 33.3091L64.0564 45.5544C66.675 47.1011 66.675 50.8931 64.0564 52.4398L43.3524 64.6851C40.6857 66.2638 37.315 64.3384 37.315 61.2398V36.7544C37.315 33.6558 40.6857 31.7304 43.3524 33.3091Z" fill="white" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        <?= Html::endTag('div') ?>
    <?php } else {

        $sPicture = $arItem['PREVIEW_PICTURE'];

        if (empty($sPicture))
            $sPicture = $arItem['DETAIL_PICTURE'];

        if (!empty($sPicture)) {
            $sPicture = CFile::ResizeImageGet(
                $sPicture, [
                'width' => 300,
                'height' => 300
            ],
                BX_RESIZE_IMAGE_PROPORTIONAL_ALT
            );

            if (!empty($sPicture))
                $sPicture = $sPicture['src'];
        }

        if (empty($sPicture))
            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

    ?>
        <?= Html::tag($sTag, '', [
            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
            'class' => 'widget-item-picture',
            'data' => [
                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
            ],
            'style' => [
                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
            ],
            'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
        ]) ?>
    <?php } ?>
<?php } ?>