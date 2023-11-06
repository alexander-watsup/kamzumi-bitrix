<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$vChildrenRender = include(__DIR__.'/parts/children.php');

if ($arVisual['PICTURE']['SIZE'] === 'small') {
    $arPictureSize = [
        'width' => 80,
        'height' => 50
    ];
} else {
    $arPictureSize = [
        'width' => 160,
        'height' => 100
    ];
}
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-services',
        'c-services-template-19'
    ],
    'data' => [
        'svg-file-use' => $arVisual['SVG']['USE'] ? 'true' : 'false'
    ]
]) ?>
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <div class="widget-content">
                <div class="widget-sections">
                    <div class="intec-grid intec-grid-wrap intec-grid-i-24">
                        <div class="intec-grid-item-4 intec-grid-item-768-1">
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                                </div>
                            <?php } ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'widget-sections-tabs'
                                ],
                                'data' => [
                                    'role' => 'section.tabs'
                                ]
                            ]) ?>
                                <?php $bFirst = true ?>
                                <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
                                    <?= Html::tag('div', $arSection['NAME'], [
                                        'class' => Html::cssClassFromArray([
                                            'widget-sections-tab' => true,
                                            'intec-cl-border' => $bFirst,
                                            'intec-cl-border-hover' => true
                                        ], true),
                                        'data' => [
                                            'role' => 'section.tabs.item',
                                            'id' => $arSection['ID'],
                                            'active' => $bFirst ? 'true' : 'false'
                                        ]
                                    ]) ?>
                                    <?php if ($bFirst) $bFirst = false ?>
                                <?php } ?>
                            <?= Html::endTag('div') ?>
                        </div>
                        <div class="intec-grid-item intec-grid-item-768-1">
                            <div class="widget-sections-content" data-role="section.content">
                                <?php $bFirst = true ?>
                                <?php foreach ($arResult['SECTIONS'] as $arSection) {

                                    $sId = $sTemplateId.'_'.$arSection['ID'];
                                    $sAreaId = $this->GetEditAreaId($sId);
                                    $this->AddEditAction($sId, $arSection['EDIT_LINK']);
                                    $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);

                                    $arPicture = [
                                        'TYPE' => 'picture',
                                        'SOURCE' => null,
                                        'ALT' => null,
                                        'TITLE' => null
                                    ];

                                    if (!empty($arSection['PICTURE'])) {
                                        if ($arSection['PICTURE']['CONTENT_TYPE'] === 'image/svg+xml') {
                                            $arPicture['TYPE'] = 'svg';
                                            $arPicture['SOURCE'] = $arSection['PICTURE']['SRC'];
                                        } else {
                                            $arPicture['SOURCE'] = CFile::ResizeImageGet($arSection['PICTURE'], $arPictureSize, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                            if (!empty($arPicture['SOURCE'])) {
                                                $arPicture['SOURCE'] = $arPicture['SOURCE']['src'];
                                            } else {
                                                $arPicture['SOURCE'] = null;
                                            }
                                        }
                                    }

                                    if (empty($arPicture['SOURCE'])) {
                                        $arPicture['TYPE'] = 'picture';
                                        $arPicture['SOURCE'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                                    } else {
                                        $arPicture['ALT'] = $arSection['PICTURE']['ALT'];
                                        $arPicture['TITLE'] = $arSection['PICTURE']['TITLE'];
                                    }
                                    ?>
                                    <?= Html::beginTag('div', [
                                        'class' => 'widget-sections-content-item',
                                        'data' => [
                                            'role' => 'section.content.item',
                                            'id' => $arSection['ID'],
                                            'active' => $bFirst ? 'true' : 'false'
                                        ]
                                    ]) ?>
                                        <div class="widget-item" id="<?= $sAreaId ?>">
                                            <?= Html::beginTag('div', [
                                                'class' => [
                                                    'widget-item-wrapper',
                                                    'intec-grid',
                                                    'intec-grid-wrap',
                                                    'intec-grida-v-start'
                                                ]
                                            ])?>
                                                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                                    <div class="intec-grid-item-auto intec-grid-item-600-1">
                                                        <?= Html::beginTag('a', [
                                                            'class' => 'widget-item-picture-wrap',
                                                            'href' => $arSection['SECTION_PAGE_URL'],
                                                            'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                                        ]) ?>
                                                        <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                                            <?= Html::tag('div', FileHelper::getFileData('@root/'.$arPicture['SOURCE']), [
                                                                'class' => [
                                                                    Html::cssClassFromArray([
                                                                        'widget-item-picture' => true,
                                                                        'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                                                        'intec-image-effect' => true,
                                                                    ], true)
                                                                ]
                                                            ]) ?>
                                                        <?php } else { ?>
                                                            <?= Html::tag('div', null, [
                                                                'class' => [
                                                                    'widget-item-picture',
                                                                    'intec-image-effect'
                                                                ],
                                                                'data' => [
                                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                                                ],
                                                                'style' => [
                                                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arPicture['SOURCE'].'\')' : null
                                                                ]
                                                            ]) ?>
                                                        <?php } ?>
                                                        <?= Html::endTag('a') ?>
                                                    </div>
                                                <?php } ?>
                                                <div class="intec-grid-item intec-grid-item-600-1">
                                                    <div class="widget-item-content">
                                                        <div class="intec-grid-item">
                                                            <div class="widget-item-name-wrap">
                                                                <?= Html::tag('a', $arSection['NAME'], [
                                                                    'class' => [
                                                                        'widget-item-name',
                                                                        'intec-cl-text-hover'
                                                                    ],
                                                                    'href' => $arSection['SECTION_PAGE_URL'],
                                                                    'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                                                                ]) ?>
                                                            </div>
                                                            <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arSection['DESCRIPTION'])) { ?>
                                                                <div class="widget-item-description">
                                                                    <?= $arSection['DESCRIPTION'] ?>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if ($arVisual['CHILDREN']['SHOW'] &&  !empty($arSection['ITEMS'])) { ?>
                                                                <div class="widget-item-children">
                                                                    <div class="intec-grid intec-grid-i-v-4 intec-grid-i-h-12 intec-grid-wrap">
                                                                        <?php $vChildrenRender($arSection['ITEMS']) ?>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?= Html::endTag('div') ?>
                                            <?= Html::tag('a', Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_19_BUTTON_MORE'), [
                                                'class' => [
                                                    'widget-item-button',
                                                    'intec-cl-background-hover',
                                                    'intec-cl-border-hover'
                                                ],
                                                'href' => $arSection['SECTION_PAGE_URL'],
                                                'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                                            ]) ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                    <?php if ($bFirst) $bFirst = false ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>