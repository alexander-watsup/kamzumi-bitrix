<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (empty($arResult['ITEMS']))
    return;

?>
<div id="<?= $sTemplateId ?>" class="ns-intec-seo c-filter-tags c-filter-tags-default" data-initialized="false" data-expandable="false" data-expanded="false">
    <div class="filter-tags-items" data-role="items">
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
            <?= Html::beginTag('div', [
                'class' => 'filter-tags-item',
                'data' => [
                    'active' => $arItem['ACTIVE'] ? 'true' : 'false',
                    'role' => 'item'
                ]
            ]) ?>
                <?= Html::tag($arItem['ACTIVE'] ? 'div' : 'a', Html::encode($arItem['NAME']), [
                    'href' => !$arItem['ACTIVE'] ? Html::encode($arItem['TARGET'] ? $arItem['URL']['TARGET'] : $arItem['URL']['SOURCE']) : null,
                    'class' => 'filter-tags-item-name'
                ]) ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
    <div class="filter-tags-buttons">
        <a class="filter-tags-button intec-cl-border" data-role="button" data-action="expand">
            <?= Loc::getMessage('C_FILTER_TAGS_DEFAULT_BUTTONS_EXPAND') ?>
        </a>
        <a class="filter-tags-button intec-cl-border" data-role="button" data-action="collapse">
            <?= Loc::getMessage('C_FILTER_TAGS_DEFAULT_BUTTONS_COLLAPSE') ?>
        </a>
    </div>
    <script type="text/javascript">
        (function () {
            var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
            var container = $('[data-role="items"]', root).first();
            var items = $('[data-role="item"]', container);
            var button = $('[data-role="button"]', root);
            var rows = 2;
            var state = false;

            var getExpandedHeight = function () {
                var value = container.css('height');
                var result;

                container.css('height', '');
                result = container.height();
                container.css('height', value);

                return result;
            };

            var getCollapsedHeight = function () {
                return items.outerHeight(false) * rows;
            };

            var hasHiddenItems = function () {
                var result = false;

                items.each(function () {
                    var item = $(this);
                    var height = getCollapsedHeight();

                    if (item.offset().top - container.offset().top >= height) {
                        result = true;
                        return false;
                    }
                });

                return result;
            };

            var expand = function () {
                if (state)
                    return;

                state = true;
                container.stop().animate({
                    'height': getExpandedHeight()
                }, 250, function () {
                    container.css('height', '');
                    root.attr('data-expanded', 'true');
                });
            };

            var collapse = function () {
                if (!state)
                    return;

                state = false;
                container.stop().animate({
                    'height': getCollapsedHeight()
                }, 250, function () {
                    root.attr('data-expanded', 'false');
                });
            };

            var toggle = function () {
                if (hasHiddenItems())
                    state ? collapse() : expand();
            };

            if (hasHiddenItems()) {
                container.css('height', getCollapsedHeight());
                root.attr('data-expandable', 'true');
            }

            $(window).on('resize', function () {
                 if (state) {
                     container.css('height', '');
                 } else {
                     if (hasHiddenItems()) {
                         container.css('height', getCollapsedHeight());
                         root.attr('data-expandable', 'true');
                     } else {
                         root.attr('data-expandable', 'false');
                     }
                 }
            });

            button.on('click', toggle);
            root.attr('data-initialized', 'true');
        })();
    </script>
</div>
