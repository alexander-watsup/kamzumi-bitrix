<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var bool $bSkuDynamic
 */

?>
<?php $vGallery = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual) { ?>
    <?php if (empty($arItem['GALLERY']) && $bOffer) return ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-gallery',
        'data' => [
            'role' => 'gallery',
            'offer' => $bOffer ? $arItem['ID'] : 'false'
        ]
    ]) ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-element-gallery-pictures',
            'data' => [
                'role' => 'gallery.pictures',
                'action' => $arVisual['GALLERY']['ACTION'],
                'zoom' => $arVisual['GALLERY']['ZOOM'] ? 'true' : 'false'
            ]
        ]) ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'catalog-element-gallery-pictures-slider' => true,
                    'owl-carousel' => count($arItem['GALLERY']) > 1
                ], true),
                'data-role' => count($arItem['GALLERY']) > 1 ? 'gallery.pictures.slider' : null
            ]) ?>
                <?php if (!empty($arItem['GALLERY'])) { ?>
                    <?php foreach ($arItem['GALLERY'] as $arPicture) {

                        $sPicture = CFile::ResizeImageGet($arPicture, [
                            'width' => 600,
                            'height' => 600
                        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                    ?>
                        <div class="catalog-element-gallery-pictures-slider-item" data-role="gallery.pictures.item">
                            <?= Html::beginTag($arVisual['GALLERY']['ACTION'] === 'source' ? 'a' : 'div', [
                                'class' => 'catalog-element-gallery-pictures-slider-item-picture',
                                'href' => $arVisual['GALLERY']['ACTION'] === 'source' ? $arPicture['SRC'] : null,
                                'target' => $arVisual['GALLERY']['ACTION'] === 'source' ? '_blank' : null,
                                'data-role' => 'gallery.pictures.item.picture',
                                'data-src' => $arVisual['GALLERY']['ACTION'] === 'popup' || $arVisual['GALLERY']['ZOOM'] ? $arPicture['SRC'] : null
                            ]) ?>
                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture['src'], [
                                    'alt' => $arResult['NAME'],
                                    'title' => $arResult['NAME'],
                                    'loading' => 'lazy',
                                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture['src'] : null
                                ]) ?>
                                <div class="intec-aligner"></div>
                            <?= Html::endTag($arVisual['GALLERY']['ACTION'] === 'source' ? 'a' : 'div') ?>
                        </div>
                    <?php } ?>
                    <?php unset($arPicture, $sPicture) ?>
                <?php } else { ?>
                    <div class="catalog-element-gallery-pictures-slider-item">
                        <div class="catalog-element-gallery-pictures-slider-item-picture">
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png', [
                                'alt' => $arResult['NAME'],
                                'title' => $arResult['NAME'],
                                'loading' => 'lazy',
                                'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'data-original' => $arVisual['LAZYLOAD']['USE'] ? SITE_TEMPLATE_PATH.'/images/picture.missing.png' : null
                            ]) ?>
                            <div class="intec-aligner"></div>
                        </div>
                    </div>
                <?php } ?>
            <?= Html::endTag('div') ?>
        <?= Html::endTag('div') ?>
        <?php if ($arVisual['GALLERY']['PREVIEW'] && count($arItem['GALLERY']) > 1) { ?>
            <div class="catalog-element-gallery-preview" data-role="gallery.preview">
                <div class="catalog-element-gallery-preview-slider owl-carousel" data-role="gallery.preview.slider">
                    <?php $bFirst = true ?>
                    <?php foreach ($arItem['GALLERY'] as $arPicture) {

                        $sPicture = CFile::ResizeImageGet($arPicture, [
                            'width' => 64,
                            'height' => 64
                        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT)

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => 'catalog-element-gallery-preview-slider-item',
                            'data' => [
                                'role' => 'gallery.preview.slider.item',
                                'active' => $bFirst ? 'true' : 'false'
                            ]
                        ]) ?>
                            <div class="catalog-element-gallery-preview-slider-item-picture">
                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture['src'], [
                                    'alt' => $arResult['NAME'],
                                    'title' => $arResult['NAME'],
                                    'loading' => 'lazy',
                                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture['src'] : null
                                ]) ?>
                                <div class="intec-aligner"></div>
                            </div>
                        <?= Html::endTag('div') ?>
                        <?php if ($bFirst) $bFirst = false ?>
                    <?php } ?>
                </div>
                <div class="catalog-element-gallery-preview-navigation" data-role="gallery.preview.navigation"></div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>
<div class="catalog-element-gallery-container catalog-element-main-block">
    <?php $vGallery($arResult);

    if ($bSkuDynamic) {
        foreach ($arResult['OFFERS'] as &$arOffer)
            $vGallery($arOffer, true);

        unset($arOffer);
    } ?>
</div>
<?php unset($vGallery) ?>