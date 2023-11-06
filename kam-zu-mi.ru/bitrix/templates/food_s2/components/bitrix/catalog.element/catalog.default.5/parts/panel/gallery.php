<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var bool $bOffers
 */

?>
<?php $vPanelGallery = function (&$arItem, $bOffer = false) use (&$arVisual) { ?>
    <?php if (empty($arItem['GALLERY']) && $bOffer) return ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-panel-gallery',
        'data' => [
            'role' => 'gallery',
            'offer' => $bOffer ? $arItem['ID'] : 'false'
        ]
    ]) ?>
        <?php if (!empty($arItem['GALLERY'])) {

            $arPicture = CFile::ResizeImageGet(ArrayHelper::getFirstValue($arItem['GALLERY']), [
                'width' => 74,
                'height' => 64
            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

        ?>
            <div class="catalog-element-panel-gallery-picture intec-image-effect">
                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arPicture['src'], [
                    'alt' => $arItem['NAME'],
                    'title' => $arItem['NAME'],
                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['src'] : null
                ]) ?>
            </div>
        <?php } else { ?>
            <div class="catalog-element-panel-gallery-picture intec-image-effect">
                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png', [
                    'alt' => $arItem['NAME'],
                    'title' => $arItem['NAME'],
                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? SITE_TEMPLATE_PATH.'/images/picture.missing.png' : null
                ]) ?>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php $vPanelGallery($arResult);

if ($bOffers) {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $vPanelGallery($arOffer, true);

    unset($arOffer);
}

unset($vPanelGallery) ?>
