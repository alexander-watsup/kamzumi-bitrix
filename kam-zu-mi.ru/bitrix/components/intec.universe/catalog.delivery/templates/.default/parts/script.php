<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();?>
<?php

use intec\core\helpers\JavaScript;

?>
<script>
    (function ($, api) {

        var cityID = '<?= JavaScript::toObject($arParams['CITY_ID']) ?>';
        var quantity = <?= JavaScript::toObject($arParams['PRODUCT_QUANTITY_VALUE']) ?>;
        var useBasket = <?=($arParams['USE_BASKET'] == 'Y')?'true':'false' ?>;

        locationUpdated = function(id) {
            var location = this.getNodeByLocationId(id);

            var cityName = location.DISPLAY;
            cityID = location.CODE;

            root.deliveries.update();
            root.cityBlock.current.html(cityName);
            root.cityBlock.attr('data-expanded', false);
        }

        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);

        root.cityBlock = $('[data-role="cityBlock"]', root);
        root.cityBlock.current = $('[data-role="currentCity"]', root);
        root.cityBlock.current.on('click', function() {
            if (root.cityBlock.attr('data-expanded') == 'true') {
                root.cityBlock.attr('data-expanded', false);
            } else {
                root.cityBlock.attr('data-expanded', true);
            }
        });

        root.counter = $('[data-role="counter"]', root);
        root.quantity = api.controls.numeric({
            'value': <?= JavaScript::toObject($arParams['PRODUCT_QUANTITY_VALUE']) ?>,
            'bounds': {
                'minimum': <?= JavaScript::toObject($arParams['PRODUCT_QUANTITY_MIN']) ?>,
                'maximum': <?=$arParams['PRODUCT_QUANTITY_MAX'] ?>
            },
            'step': <?= JavaScript::toObject($arParams['PRODUCT_QUANTITY_STEP']) ?>
        }, root.counter);
        root.quantity.on('change', function () {
            quantity = root.quantity.get();
            root.deliveries.update();
        });

        root.useBasket = $('[data-role="useBasket"]', root);
        root.useBasket.on('change', function () {
            if ($(this).prop('checked')) {
                useBasket = true;
            } else
                useBasket = false;

            root.deliveries.update();
        });

        root.deliveries = $('[data-role="deliveries"]', root);
        root.deliveries.update = function() {
            $.ajax({
                type: "POST",
                url: "<?= $component->getPath().'/ajax.php'?>",
                data: {
                    'delivery': {
                        'ajax': 'y'
                    },
                    'cityID': cityID,
                    'quantity': quantity,
                    'useBasket': useBasket ? 'y':'n',
                    'template': <?= JavaScript::toObject($templateName)?>,
                    'params': <?= JavaScript::toObject($arParams) ?>
                },
                success: function (response) {
                    root.deliveries.html(response);
                }
            });
        }

        $(root).on('click', '[data-role="buttonDetails"]', function(){
            var delivery = $(this).closest('[data-role="delivery"]');
            var blockDetails = delivery.find('[data-role="blockDetails"]');
            $(this).toggleClass('delivery-element-button-more-opened');

            if (blockDetails.attr('data-expanded') == 'true') {
                blockDetails.slideUp(400);
                blockDetails.attr('data-expanded', false);
            } else {
                blockDetails.slideDown(400);
                blockDetails.attr('data-expanded', true);
            }
        });
    })(jQuery, intec)
</script>