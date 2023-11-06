<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$this->addExternalJS($templateFolder."/js/circles.js");

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$iCounter = 0;
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => 'widget c-advantages c-advantages-template-32'
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
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
                </div>
            <?php } ?>
            <div class="widget-content">
                <div class="widget-items-wrap">
                    <div class="widget-items intec-grid intec-grid-wrap">
                        <?php foreach ($arResult['ITEMS'] as $arItem) {

                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $arData = $arItem['DATA'];

                            if (empty($arData['NUMBER']) || empty($arData['MAX_NUMBER']))
                                continue;
                            ?>
                            <?= Html::beginTag('div', [
                                'id' => $sAreaId,
                                'class' => [
                                    Html::cssClassFromArray([
                                        'widget-item' => true,
                                        'intec-grid-item' => [
                                            $arVisual['COLUMNS'] => true,
                                            '500-1' => true,
                                            '768-2' => $arVisual['COLUMNS'] >= 2,
                                            '900-3' => $arVisual['COLUMNS'] >= 3,
                                            '1100-4' => $arVisual['COLUMNS'] >= 4
                                        ],
                                    ], true)
                                ],
                                'data' => [
                                    'role' => 'item'
                                ]
                            ]) ?>
                                <div class="widget-item-wrapper intec-cl-text">
                                    <?= Html::tag('div', '', [
                                        'class' => [
                                            'widget-item-diagramm'
                                        ],
                                        'data' => [
                                            'role' => 'item.diagramm',
                                            'max-value' => $arData['MAX_NUMBER'],
                                            'value' => $arData['NUMBER'],
                                            'animated' => 'false'
                                        ],
                                        'id' => 'circle-'.$sId
                                    ]) ?>
                                    <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                        <div class="widget-item-description">
                                            <?= $arItem['PREVIEW_TEXT'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>

<script>
    (function(){
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

        items.each(function () {
            var self = $(this);
            var diagramm = $('[data-role="item.diagramm"]', self);
            var maxValue = diagramm.attr('data-max-value');

            circle = Circles.create({
                'id': diagramm.attr('id'),
                'radius': 80,
                'value': 0,
                'maxValue': maxValue,
                'width': 8,
                'styleText': true,
                'text': function(value){ return Math.round(value);},
                'colors': ['#E7F1FF', '#0065ff'],
                'duration': 1000
            });

            myCircles.push(circle);
        });

        $(document).on('scroll', function () {
            items.each(function () {
                var self = $(this);
                var isVisible = isElementViewed(self, thisWindow);

                if (isVisible) {
                    var diagramm = $('[data-role="item.diagramm"]', self);
                    var animated = diagramm.attr('data-animated');

                    if (animated === 'false') {
                        var diagrammIndex = self.index();
                        var value = diagramm.attr('data-value');

                        animated = 'true';
                        diagramm.attr('data-animated', animated);

                        myCircles[diagrammIndex].update(value);
                    }
                }
            });
        });
    })();
</script>
