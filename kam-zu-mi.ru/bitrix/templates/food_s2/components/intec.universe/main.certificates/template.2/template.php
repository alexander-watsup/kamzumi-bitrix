<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];


/**
 * @var Closure $vItems()
 */

?>
<div class="widget c-certificates c-certificates-template-2" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arVisual['WIDE']) { ?>
                    <div class="intec-content">
                        <div class="intec-content-wrapper">
                            <?php } ?>
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                                <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['WIDE']) { ?>
                        </div>
                    </div>
                <?php } ?>
                </div>
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => 'widget-content',
                'data' => [
                    'indent' => $arVisual['INDENT']['USE'] ? $arVisual['INDENT']['VALUE'] : null
                ]
            ]) ?>
            <?php if (!empty($arResult['ITEMS'])) { ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'owl-carousel' => $arVisual['SLIDER']['USE'],
                        'widget-items' => true,
                        'intec-grid' => [
                            '' => true,
                            'wrap' => true,
                            'a-v-stretch' => true
                        ]
                    ], true),
                    'data' => [
                        'role' => 'items'
                    ]
                ]) ?>
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $sPicture = $arItem['PREVIEW_PICTURE'];

                    if (empty($sPicture))
                        $sPicture = $arItem['DETAIL_PICTURE'];

                    $sPictureResized = CFile::ResizeImageGet($sPicture, [
                        'width' => 700,
                        'height' => 700
                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                    if (!empty($sPictureResized)) {
                        $sPictureResized = $sPictureResized['src'];
                    } else {
                        $sPictureResized = $sPicture['SRC'];
                    }

                    $sPictureSize = CFile::FormatSize($sPicture['FILE_SIZE']);

                    $sPicture = $sPicture['SRC'];

                    ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'widget-item' => true,
                            'intec-grid-item' => [
                                $arVisual['COLUMNS'] => true,
                                '1200-4' => $arVisual['COLUMNS'] >= 5,
                                '768-3' => $arVisual['COLUMNS'] >= 4,
                                '600-2' => $arVisual['COLUMNS'] >= 3,
                                '450-1' => true
                            ]
                        ], true),
                        'data' => [
                            'role' => 'item',
                            'src' => $sPicture,
                            'preview-src' => $sPictureResized
                        ]
                    ]) ?>
                    <div class="widget-item-wrapper" id="<?= $sAreaId ?>">
                        <?= Html::beginTag('div', [
                            'class' => 'widget-item-picture',
                            'title' => Html::decode(Html::stripTags($arItem['NAME'])),
                            'data' => [
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPictureResized : null
                            ],
                            'style' => [
                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPictureResized.'\')' : null
                            ]
                        ]) ?>
                            <span class="widget-item-picture-icon intec-cl-background">
                                <i class="fal fa-search-plus"></i>
                            </span>
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPictureResized, [
                                'alt' => $arItem['NAME'],
                                'title' => $arItem['NAME'],
                                'loading' => 'lazy',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPictureResized : null
                                ]
                            ]) ?>
                        <?= Html::endTag('div') ?>
                        <div class="widget-item-name">
                            <?= $arItem['NAME'] ?>
                        </div>
                        <div class="widget-item-size">
                            <?= $sPictureSize ?>
                        </div>
                    </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?= Html::endTag('div') ?>
            <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
                <div class="widget-footer align-<?= $arBlocks['FOOTER']['POSITION'] ?>">
                    <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                        <?= Html::tag('a', $arBlocks['FOOTER']['BUTTON']['TEXT'], [
                            'href' => $arBlocks['FOOTER']['BUTTON']['LINK'],
                            'class' => [
                                'widget-footer-button',
                                'intec-ui' => [
                                    '',
                                    'size-5',
                                    'scheme-current',
                                    'control-button',
                                    'mod' => [
                                        'transparent',
                                        'round-half'
                                    ]
                                ]
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php include(__DIR__.'/parts/script.php') ?>