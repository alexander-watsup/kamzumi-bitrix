<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$iCounter = 0;
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => 'widget c-advantages c-advantages-template-34',
    'data' => [
        'theme' => $arVisual['THEME'],
        'button-align' => $arVisual['BUTTON']['ALIGN']
    ],
    'style' => $arVisual['BACKGROUND']['SHOW'] ? 'background: '.$arVisual['BACKGROUND']['COLOR'] : null
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => [
                    'widget-wrapper',
                    'intec-grid',
                    'intec-grid-wrap',
                    'intec-grid-a-v-center'
                ]
            ]) ?>
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                    <div class="widget-header intec-grid-item-3 intec-grid-item-1000-1">
                        <div class="widget-header-wrapper">
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
                            <?php if ($arVisual['BUTTON']['SHOW']) { ?>
                                <div class="widget-button-wrap">
                                    <?= Html::tag('a', Html::stripTags($arVisual['BUTTON']['TEXT']), array(
                                        'href' => $arVisual['BUTTON']['LINK'],
                                        'class' => array(
                                            'widget-button',
                                            'intec-ui' => array(
                                                '',
                                                'control-button',
                                                'mod-round-2',
                                                'size-2',
                                                'scheme-current'
                                            )
                                        )
                                    )) ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="widget-content intec-grid-item">
                    <div class="widget-items-wrap">
                        <div class="widget-items">
                            <?php foreach ($arResult['ITEMS'] as $arItem) {

                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $arData = $arItem['DATA'];

                                if (empty($arData['NUMBER']) || empty($arData['MAX_NUMBER']))
                                    continue;

                                $iPercent = $arData['NUMBER'] * 100 / $arData['MAX_NUMBER'];
                                $iPercent = round($iPercent).'%';
                                ?>
                                <?= Html::beginTag('div', [
                                    'id' => $sAreaId,
                                    'class' => [
                                        'widget-item'
                                    ],
                                    'data' => [
                                        'role' => 'item'
                                    ]
                                ]) ?>
                                    <div class="widget-item-wrapper">
                                        <div class="widget-item-wrapper-2">
                                            <?= Html::beginTag('div', [
                                                'id' => 'diagramm-'.$sAreaId,
                                                'class' => [
                                                    'widget-item-diagramm'
                                                ],
                                                'data' => [
                                                    'role' => 'item.diagramm',
                                                    'value' => $arData['NUMBER'],
                                                    'max-value' => $arData['MAX_NUMBER'],
                                                    'animated' => 'false'

                                                ]
                                            ]) ?>
                                                <div class="widget-item-information">
                                                    <div class="intec-grid intec-grid-a-v-center">
                                                        <div class="intec-grid-item widget-item-name" data-role="diagramm.name">
                                                            <?= $arItem['NAME'] ?>
                                                        </div>
                                                        <div class="intec-grid-item-auto widget-item-count" data-role="diagramm.value">
                                                            <?= $iPercent ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="widget-item-line" data-role="diagramm.line">
                                                    <span class="widget-item-subline intec-cl-background"></span>
                                                </div>
                                            <?= Html::endTag('div') ?>
                                        </div>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?= Html::endTag('div') ?>

            <script>
                (function () {
                    var thisWindow = $(window);
                    var root = $('#<?= $sTemplateId ?>');
                    var items = $('[data-role="item"]', root);
                    var myCircles = [];

                    function isElementViewed($element, $window) {
                        var docViewTop = $window.scrollTop();
                        var docViewBottom = docViewTop + $window.height();

                        var elementTop = $element.offset().top;
                        var elementBottom = elementTop + $element.height();

                        return ((elementBottom <= docViewBottom) && (elementTop >= docViewTop));
                    }

                    $(document).on('scroll', function () {
                        items.each(function () {
                            var self = $(this);
                            var isVisible = isElementViewed(self, thisWindow);

                            if (isVisible) {
                                var diagramm = $('[data-role="item.diagramm"]', self);
                                var animated = diagramm.attr('data-animated');

                                if (animated === 'false') {

                                    animated = 'true';
                                    diagramm.attr('data-animated', animated);

                                    var value = diagramm.attr('data-value');
                                    var maxValue = diagramm.attr('data-max-value');

                                    var bLine = $('[data-role="diagramm.line"]', diagramm);
                                    var bSubLine = $('span', bLine);

                                    var percentage = 0;
                                    percentage = (value * 100) / maxValue;

                                    bSubLine.css('width', percentage+'%');

                                }
                            }
                        });
                    });
                })();
            </script>

        </div>
    </div>
<?= Html::endTag('div') ?>