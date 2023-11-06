<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arVisual
 * @var array $arForm
 * @var string $sTemplateId
 */
?>
<script type="text/javascript">
    (function () {
        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        var items = $('[data-role="items"]', root);
        var checkbox = [];
        var updateButton;

        button.order = $('[data-role="button.order"]', root);
        button.clear = $('[data-role="button.clear"]', root);
        checkbox.all = $('[data-role="checkbox.all"]', root);
        checkbox.items = $('[data-role="checkbox.item"]', root);

        function declOfNum(number, titles) {
            cases = [2, 0, 1, 1, 1, 2];
            return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
        }

        updateButton = function() {
            var counter = $('[data-role="checkbox.item"]:checked', root).length;
            var buttonText = '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT') ?>';

            text = declOfNum(counter, [
                '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT_2_1') ?>',
                '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT_2_3') ?>',
                '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT_2_2') ?>'
            ]);

            if (counter > 0) {
                buttonText = '<?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_23_ORDER_BUTTON_DEFAULT') ?> ' + counter + ' ' + text;
            }

            button.order.html(buttonText);
        }

        checkbox.all.on('change', function() {
            var self = $(this);
            var checked = '';

            if (self.is(":checked")) {
                checked = 'checked';
            } else {
                checked = '';
            }

            checkbox.items.each(function() {
                $(this).prop('checked', checked);
            });

            updateButton();
        });


        checkbox.items.on('change', function () {
            updateButton();
        });

        button.clear.on('click', function() {
            checkbox.items.each(function() {
                $(this).prop('checked', '');
            });

            checkbox.all.prop('checked', '');

            updateButton();
        });

        button.order.on('click', function() {
            var options = <?= JavaScript::toObject([
                'id' => $arForm['ID'],
                'template' => $arForm['TEMPLATE'],
                'parameters' => [
                    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                    'CONSENT_URL' => $arForm['CONSENT']
                ],
                'settings' => [
                    'title' => $arForm['TITLE']
                ]
            ]) ?>;

            options.fields = {};

            <?php if (!empty($arForm['FIELD'])) { ?>
            var items = []
            var name = '';

            checkbox.items.each(function() {
                var self = $(this);

                if (self.is(":checked")) {
                    var value;

                    value = self.closest('[data-role="item"]').find('[data-role="item.name"]').html();

                    items.push(value);
                }
            });

            name = items.join(', ');

            options.fields[<?= JavaScript::toObject($arForm['FIELD']) ?>] = name;
            <?php } ?>

            universe.forms.show(options);

            if (window.yandex && window.yandex.metrika) {
                window.yandex.metrika.reachGoal('forms.open');
                window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arForm['ID'].'.open') ?>);
            }
        });
    })();
</script>
