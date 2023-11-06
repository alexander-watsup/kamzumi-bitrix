<?php if (!defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var string $sTemplateContainer
 * @var array $arVisual
 * @var array $arNavigation
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$bBase = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;

if ($bBase)
    CJSCore::Init(array('currency'));

?>
<script type="text/javascript">
    (function ($, api) {
        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);

        $(function () {
            var properties = root.data('properties');
            var items;
            var order;
            var quickViewShow;
            var quickItemsId = [];
            var quickItems = Object.create(null);

            <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
                quickViewShow = function (data, quickItemsId) {
                    universe.components.show({
                        'component': 'bitrix:catalog.element',
                        'template': data.template,
                        'parameters': data.parameters,
                        'settings': {
                            'parameters': {
                                'className': 'popup-window-quick-view',
                                'width': null
                            }
                        }
                    }, function(){
                        <?php if ($arResult['QUICK_VIEW']['SLIDE']['USE']) { ?>
                        var container = $(this.contentContainer);

                        var indexItem = quickItemsId.indexOf(data.parameters.ELEMENT_ID);
                        var prevItemId = quickItemsId[indexItem - 1];
                        var nextItemId = quickItemsId[indexItem + 1];

                        if (prevItemId === undefined)
                            prevItemId = 0;

                        if (nextItemId === undefined)
                            nextItemId = 0;

                        var load = container.find('.popup-load-container');

                        load.css('display', 'none');

                        container.append('<div class="popup-load-container"><div class="popup-load-whirlpool"></div></div>');

                        if (prevItemId !== 0 || nextItemId !== 0) {
                            container.append('<div class="popup-button btn-prev intec-cl-background-hover" data-role="quickView.button" data-id="' + prevItemId + '">' +
                                '<i class="far fa-angle-left"></i>' +
                                '</div>');
                            container.append('<div class="popup-button btn-next intec-cl-background-hover" data-role="quickView.button" data-id="' + nextItemId + '">' +
                                '<i class="far fa-angle-right"></i>' +
                                '</div>');
                        }
                        <?php } ?>
                    });
                }

                <?php if ($arResult['QUICK_VIEW']['SLIDE']['USE']) { ?>
                    $('body').on('click', '[data-role="quickView.button"]', function () {
                        var item = $(this);
                        var id = item.data('id');

                        item.parent().find('.popup-load-container').css('display', 'block');

                        quickViewShow(quickItems[id], quickItemsId);
                    });
                <?php } ?>
            <?php } ?>

            <?php if ($arResult['FORM']['SHOW']) { ?>
                order = function (data) {
                    var options = <?= JavaScript::toObject([
                        'id' => $arResult['FORM']['ID'],
                        'template' => $arResult['FORM']['TEMPLATE'],
                        'parameters' => [
                            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                            'CONSENT_URL' => $arResult['URL']['CONSENT']
                        ],
                        'settings' => [
                            'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_FORM_TITLE')
                        ]
                    ]) ?>;

                    options.fields = {};

                    <?php if (!empty($arResult['FORM']['PROPERTIES']['PRODUCT'])) { ?>
                        options.fields[<?= JavaScript::toObject($arResult['FORM']['PROPERTIES']['PRODUCT']) ?>] = data.name;
                    <?php } ?>

                    universe.forms.show(options);

                    if (window.yandex && window.yandex.metrika) {
                        window.yandex.metrika.reachGoal('forms.open');
                        window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ID'].'.open') ?>);
                    }
                };
            <?php } ?>

            root.update = function () {
                var handled = [];

                if (api.isDeclared(items))
                    handled = items.handled;

                items = $('[data-role="item"]', root);
                items.handled = handled;
                items.each(function () {
                    var item = $(this);
                    var data = item.data('data');
                    var entity = data;
                    var expanded = false;

                    quickItems[data.quickView.parameters.ELEMENT_ID] = data.quickView;
                    quickItemsId.push(data.quickView.parameters.ELEMENT_ID);

                    if (handled.indexOf(this) > -1)
                        return;

                    handled.push(this);
                    item.offers = new universe.catalog.offers({
                        'properties': properties.length !== 0 ? properties : data.properties,
                        'list': data.offers
                    });

                    item.image = $('[data-role="image"]', item);
                    item.counter = $('[data-role="item.counter"]', item);
                    item.price = $('[data-role="item.price"]', item);
                    item.price.base = $('[data-role="item.price.base"]', item.price);
                    item.price.discount = $('[data-role="item.price.discount"]', item.price);
                    item.order = $('[data-role="item.order"]', item);
                    item.quantity = api.controls.numeric({
                        'bounds': {
                            'minimum': entity.quantity.ratio,
                            'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                        },
                        'step': entity.quantity.ratio
                    }, item.counter);

                    <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
                        item.quickView = $('[data-role="quick.view"]', item);

                        item.quickView.on('click', function () {
                            quickViewShow(data.quickView, quickItemsId);
                        });
                    <?php } ?>

                    item.update = function () {
                        var price = null;

                        item.attr('data-available', entity.available ? 'true' : 'false');
                        api.each(entity.prices, function (index, object) {
                            if (object.quantity.from === null || item.quantity.get() >= object.quantity.from)
                                price = object;
                        });

                        if (price !== null) {
                            <?php if ($bBase && $arVisual['PRICE']['RECALCULATION']) { ?>
                            if (price.quantity.from === null && price.quantity.to === null) {
                                var summary = [];

                                summary.base = price.base.value * item.quantity.get();
                                summary.discount = price.discount.value * item.quantity.get();
                                BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                                price.base.display = BX.Currency.currencyFormat(summary.base, price.currency.CURRENCY, true);
                                price.discount.display = BX.Currency.currencyFormat(summary.discount, price.currency.CURRENCY, true);
                            }
                            <?php } ?>

                            item.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                            item.price.base.html(price.base.display);
                            item.price.discount.html(price.discount.display);
                        } else {
                            item.price.attr('data-discount', 'false');
                            item.price.base.html(null);
                            item.price.discount.html(null);
                        }

                        item.price.attr('data-show', price !== null ? 'true' : 'false');
                        item.quantity.configure({
                            'bounds': {
                                'minimum': entity.quantity.ratio,
                                'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                            },
                            'step': entity.quantity.ratio
                        });

                        item.find('[data-offer]').css({
                            'display': '',
                            'opacity': ''
                        });

                        if (entity !== data) {
                            item.find('[data-offer=' + entity.id + ']').css('display', 'block');
                            item.find('[data-offer="false"]').css('display', 'none');
                            item.find('[data-offer=' + entity.id + '][data-role="item.quantity"]').animate({'opacity': 1}, 500);

                            if (item.image.filter('[data-offer=' + entity.id + ']').length === 0)
                                item.image.filter('[data-offer="false"]').css('display', 'block');
                        }

                        item.find('[data-basket-id]')
                            .data('basketQuantity', item.quantity.get())
                            .attr('data-basket-quantity', item.quantity.get());
                    };

                    item.update();

                    <?php if ($arResult['FORM']['SHOW']) { ?>
                        item.order.on('click', function () {
                            order(data);
                        });
                    <?php } ?>

                    item.quantity.on('change', function (event, value) {
                        item.update();
                    });

                    if (!item.offers.isEmpty()) {
                        item.properties = $('[data-role="item.property"]', item);
                        item.properties.values = $('[data-role="item.property.value"]', item.properties);
                        item.properties.attr('data-visible', 'false');
                        item.properties.each(function () {
                            var self = $(this);
                            var property = self.data('property');
                            var values = self.find(item.properties.values);

                            values.each(function () {
                                var self = $(this);
                                var value = self.data('value');

                                self.on('click', function () {
                                    item.offers.setCurrentByValue(property, value);
                                });
                            });
                        });

                        api.each(item.offers.getList(), function (index, offer) {
                            api.each(offer.values, function (key, value) {
                                if (value == 0)
                                    return;

                                item.properties
                                    .filter('[data-property=' + key + ']')
                                    .attr('data-visible', 'true');
                            });
                        });

                        item.offers.on('change', function (event, offer, values) {
                            entity = offer;

                            api.each(values, function (state, values) {
                                api.each(values, function (property, values) {
                                    property = item.properties.filter('[data-property="' + property + '"]');

                                    api.each(values, function (index, value) {
                                        value = property.find(item.properties.values).filter('[data-value="' + value + '"]');
                                        value.attr('data-state', state);
                                    });
                                });
                            });

                            item.update();
                        });

                        var offerCurrent;

                        api.each(item.offers.getList(), function (index, offer) {
                            if (!offerCurrent || offerCurrent.sort > offer.sort)
                                offerCurrent = offer;
                        });

                        item.offers.setCurrentById(offerCurrent.id);
                    }

                    item.expand = function () {
                        var rectangle = item[0].getBoundingClientRect();
                        var height = rectangle.bottom - rectangle.top;

                        if (expanded)
                            return;

                        expanded = true;
                        item.css('height', height);
                        item.attr('data-expanded', 'true');
                    };

                    item.narrow = function () {
                        if (!expanded)
                            return;

                        expanded = false;
                        item.attr('data-expanded', 'false');
                        item.css('height', '');
                    };

                    item.on('mouseenter', item.expand)
                        .on('mouseleave', item.narrow);

                    $(window).on('resize', function () {
                        if (expanded) {
                            item.narrow();
                            item.expand();
                        }
                    });
                });

                <?php if ($arVisual['IMAGE']['SLIDER']) {

                    $arSlider = [
                        'items' => 1,
                        'nav' => !$arVisual['IMAGE']['NAV'],
                        'dots' => false
                    ];

                ?>
                    $(function () {
                        var slider = $('.owl-carousel', root);
                        var parameters = <?= JavaScript::toObject($arSlider) ?>

                        slider.owlCarousel({
                            'items': parameters.items,
                            'nav': parameters.nav,
                            'navText': [
                                '<i class="far fa-chevron-left"></i>',
                                '<i class="far fa-chevron-right"></i>'
                            ],
                            'dots': parameters.dots
                        });
                    });
                <?php } ?>
            };

            root.update();
        });
    })(jQuery, intec);
</script>