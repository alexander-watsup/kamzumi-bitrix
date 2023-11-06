<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sTemplateId
 * @var array $arVisual
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$bBase = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
}

$arResult['ORDER_FAST']['PARAMETERS']['AJAX_MODE'] = 'Y';
$arResult['ORDER_FAST']['PARAMETERS']['AJAX_OPTION_ADDITIONAL'] = $sTemplateId.'-order-fast';

if ($bBase) {
    CJSCore::Init(array('currency'));
}
?>
<script type="text/javascript">
    (function ($, api) {
        $(function () {
            var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
            var dynamic = $('[data-role="dynamic"]', root);
            var properties = root.data('properties');
            var data = root.data('data');
            var entity = data;

            dynamic.offers = new universe.catalog.offers({
                'properties': properties,
                'list': data.offers
            });

            window.offers = dynamic.offers;

            root.gallery = $('[data-role="gallery"]', root);
            root.gallery.pictures = $('[data-role="gallery.pictures"]', root.gallery);
            root.gallery.pictures.items = $('[data-role="gallery.picture"]', root.gallery.pictures);
            root.gallery.previews = $('[data-role="gallery.previews"]', root.gallery);
            root.gallery.previews.items = $('[data-role="gallery.preview"]', root.gallery.previews);
            dynamic.article = $('[data-role="article"]', dynamic);
            dynamic.article.value = $('[data-role="article.value"]', dynamic.article);
            dynamic.counter = $('[data-role="counter"]', dynamic);
            dynamic.price = $('[data-role="price"]', dynamic);
            dynamic.price.base = $('[data-role="price.base"]', dynamic.price);
            dynamic.price.discount = $('[data-role="price.discount"]', dynamic.price);
            dynamic.price.difference = $('[data-role="price.difference"]', dynamic.price);
            dynamic.quantity = api.controls.numeric({
                'bounds': {
                    'minimum': entity.quantity.ratio,
                    'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                },
                'step': entity.quantity.ratio
            }, dynamic.counter);
            dynamic.panel = $('[data-role="panel"]', dynamic);
            dynamic.panel.picture = $('[data-role="panel.picture"]', dynamic.panel);
            dynamic.panel.counter = $('[data-role="panel.counter"]', dynamic.panel);
            dynamic.panel.quantity = api.controls.numeric({}, dynamic.panel.counter);
            dynamic.panelMobile = $('[data-role="panel.mobile"]', dynamic);
            dynamic.purchase = $('[data-role="purchase"]', dynamic);

            <?php if (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic') { ?>
            dynamic.update = function () {
                var article = entity.article;
                var price = null;
                var quantity = {
                    'bounds': {
                        'minimum': entity.quantity.ratio,
                        'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                    },
                    'step': entity.quantity.ratio
                };

                root.attr('data-available', entity.available ? 'true' : 'false');
                root.attr('data-subscribe', entity.subscribe ? 'true' : 'false');

                if (article == null)
                    article = data.article;

                dynamic.article.attr('data-show', article == null ? 'false' : 'true');
                dynamic.article.value.text(article);

                api.each(entity.prices, function (index, object) {
                    if (object.quantity.from === null || dynamic.quantity.get() >= object.quantity.from)
                        price = object;
                });

                if (price !== null) {
                    <?php if ($bBase && $arVisual['PRICE']['RECALCULATION']) { ?>
                        var summary = [];

                        summary.base = price.base.value * dynamic.quantity.get();
                        summary.base = summary.base.toFixed(price.currency.DECIMALS);
                        summary.discount = price.discount.value * dynamic.quantity.get();
                        summary.discount = summary.discount.toFixed(price.currency.DECIMALS);
                        summary.difference = summary.base - summary.discount;
                        BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                        price.base.display = BX.Currency.currencyFormat(summary.base, price.currency.CURRENCY, true);
                        price.discount.display = BX.Currency.currencyFormat(summary.discount, price.currency.CURRENCY, true);
                        price.discount.difference = BX.Currency.currencyFormat(summary.difference, price.currency.CURRENCY, true);
                    <?php } ?>

                    dynamic.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                    dynamic.price.base.html(price.base.display);
                    dynamic.price.discount.html(price.discount.display);
                    dynamic.price.difference.html(price.discount.difference);
                } else {
                    dynamic.price.attr('data-discount', 'false');
                    dynamic.price.base.html(null);
                    dynamic.price.discount.html(null);
                    dynamic.price.difference.html(null);
                }

                dynamic.price.attr('data-show', price !== null ? 'true' : 'false');
                dynamic.quantity.configure(quantity);
                dynamic.panel.quantity.configure(quantity);

                dynamic.find('[data-offer]').css('display', '');

                if (entity !== data) {
                    dynamic.find('[data-offer=' + entity.id + ']').css('display', 'block');
                    dynamic.find('[data-offer="false"]').css('display', 'none');

                    if (root.gallery.filter('[data-offer=' + entity.id + ']').length === 0)
                        root.gallery.filter('[data-offer="false"]').css('display', '');

                    if (dynamic.panel.picture.filter('[data-offer=' + entity.id + ']').length === 0)
                        dynamic.panel.picture.filter('[data-offer="false"]').css('display', '');
                }

                dynamic.find('[data-basket-id]')
                    .data('basketQuantity', dynamic.quantity.get())
                    .attr('data-basket-quantity', dynamic.quantity.get());
            };

            dynamic.update();

            (function () {
                var update = false;

                dynamic.quantity.on('change', function (event, value) {
                    if (!update) {
                        update = true;
                        dynamic.panel.quantity.set(value);
                        dynamic.update();
                        update = false;
                    }
                });

                dynamic.panel.quantity.on('change', function (event, value) {
                    dynamic.quantity.set(value);
                });
            })();
            <?php } ?>

            <?php if ($arResult['SKU_VIEW'] == 'dynamic') { ?>
            if (!dynamic.offers.isEmpty()) {
                dynamic.properties = $('[data-role="property"]', root);
                dynamic.properties.values = $('[data-role="property.value"]', dynamic.properties);
                dynamic.properties.each(function () {
                    var self = $(this);
                    var property = self.data('property');
                    var values = self.find(dynamic.properties.values);

                    values.each(function () {
                        var self = $(this);
                        var value = self.data('value');

                        self.on('click', function () {
                            dynamic.offers.setCurrentByValue(property, value);
                        });
                    });
                });

                dynamic.offers.on('change', function (event, offer, values) {
                    entity = offer;

                    api.each(values, function (state, values) {
                        api.each(values, function (property, values) {
                            property = dynamic.properties.filter('[data-property="' + property + '"]');

                            api.each(values, function (index, value) {
                                value = property.find(dynamic.properties.values).filter('[data-value="' + value + '"]');
                                value.attr('data-state', state);
                            });
                        });
                    });

                    dynamic.update();
                });

                var offerCurrent;

                api.each(dynamic.offers.getList(), function (index, offer) {
                    if (!offerCurrent || offerCurrent.sort > offer.sort)
                        offerCurrent = offer;
                });

                dynamic.offers.setCurrentById(offerCurrent.id);
            }
            <?php } ?>

            root.gallery.each(function () {
                var gallery = $(this);
                var pictures;
                var previews;

                pictures = gallery.find(root.gallery.pictures);
                pictures.items = pictures.find(root.gallery.pictures.items);
                previews = gallery.find(root.gallery.previews);
                previews.items = previews.find(root.gallery.previews.items);

                pictures.owlCarousel({
                    'items': 1,
                    'nav': false,
                    'dots': false
                });

                <?php if ($arVisual['GALLERY']['POPUP']) { ?>
                pictures.lightGallery({
                    'share': false,
                    'selector': '.catalog-element-gallery-picture'
                });
                <?php } ?>

                <?php if ($arVisual['GALLERY']['ZOOM']) { ?>
                pictures.items.each(function () {
                    var picture = $(this);
                    var source = picture.data('src');

                    picture.zoom({
                        'url': source,
                        'touch': false
                    });
                });
                <?php } ?>

                <?php if ($arVisual['GALLERY']['SLIDER']) { ?>
                previews.owlCarousel({
                    'items': 6
                });

                previews.set = function (number) {
                    previews.items.attr('data-active', 'false');
                    previews.items.eq(number).attr('data-active', 'true');
                };

                previews.items.on('click', function () {
                    var preview = $(this);
                    var previewIndex = preview.parent('.owl-item').index();

                    pictures.trigger('to.owl.carousel', [previewIndex]);
                    previews.set(previewIndex);
                });

                pictures.on('changed.owl.carousel', function (event) {
                    previews.set(event.item.index);
                });
                <?php } ?>
            });

            <?php if ($arResult['FORM']['ORDER']['SHOW']) { ?>
            dynamic.order = $('[data-role="order"]', dynamic);
            dynamic.order.on('click', function () {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORM']['ORDER']['ID'],
                    'template' => $arResult['FORM']['ORDER']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_FORM_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORM']['ORDER']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORM']['ORDER']['PROPERTIES']['PRODUCT']) ?>] = data.name;
                <?php } ?>

                universe.forms.show(options);

                if (window.yandex && window.yandex.metrika) {
                    window.yandex.metrika.reachGoal('forms.open');
                    window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['ORDER']['ID'].'.open') ?>);
                }
            });
            <?php } ?>

            <?php if ($arResult['FORM']['CHEAPER']['SHOW']) { ?>
            dynamic.cheaper = $('[data-role="cheaper"]', dynamic);
            dynamic.cheaper.on('click', function () {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORM']['CHEAPER']['ID'],
                    'template' => $arResult['FORM']['CHEAPER']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_FORM_CHEAPER_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORM']['CHEAPER']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORM']['CHEAPER']['PROPERTIES']['PRODUCT']) ?>] = data.name;
                <?php } ?>

                universe.forms.show(options);

                if (window.yandex && window.yandex.metrika) {
                    window.yandex.metrika.reachGoal('forms.open');
                    window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['CHEAPER']['ID'].'.open') ?>);
                }
            });
            <?php } ?>

            <?php if ($arResult['FORM']['MARKDOWN']['SHOW']) { ?>
            dynamic.markdown = $('[data-role="markdown"]', dynamic);
            dynamic.markdown.on('click', function () {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['FORM']['MARKDOWN']['ID'],
                    'template' => $arResult['FORM']['MARKDOWN']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['URL']['CONSENT']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_FORM_MARKDOWN_TITLE')
                    ]
                ]) ?>;

                options.fields = {};

                <?php if (!empty($arResult['FORM']['MARKDOWN']['PROPERTIES']['PRODUCT'])) { ?>
                options.fields[<?= JavaScript::toObject($arResult['FORM']['MARKDOWN']['PROPERTIES']['PRODUCT']) ?>] = data.name;
                <?php } ?>

                universe.forms.show(options);

                if (window.yandex && window.yandex.metrika) {
                    window.yandex.metrika.reachGoal('forms.open');
                    window.yandex.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['FORM']['MARKDOWN']['ID'].'.open') ?>);
                }
            });
            <?php } ?>

            <?php if ($arResult['ORDER_FAST']['USE'] &&
        (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic')) { ?>
            dynamic.orderFast = $('[data-role="orderFast"]', dynamic);
            dynamic.orderFast.on('click', function () {
                var template = <?= JavaScript::toObject($arResult['ORDER_FAST']['TEMPLATE']) ?>;
                var parameters = <?= JavaScript::toObject($arResult['ORDER_FAST']['PARAMETERS']) ?>;

                if (intec.isNull(parameters))
                    parameters = {};

                parameters['PRODUCT'] = entity.id;
                parameters['QUANTITY'] = dynamic.quantity.get();

                universe.components.show({
                    'component': 'intec.universe:sale.order.fast',
                    'template': template,
                    'parameters': parameters,
                    'settings': {
                        'parameters': {
                            'width': null
                        }
                    }
                });
            });
            <?php } ?>

            <?php if ($arResult['DELIVERY_CALCULATION']['USE'] &&
        (empty($arResult['OFFERS']) || $arResult['SKU_VIEW'] == 'dynamic')) { ?>
            dynamic.deliveryCalculation = $('[data-role="deliveryCalculation"]', dynamic);
            dynamic.deliveryCalculation.on('click', function () {
                var template = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['TEMPLATE']) ?>;
                var parameters = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['PARAMETERS']) ?>;

                if (intec.isNull(parameters))
                    parameters = {};

                parameters['PRODUCT_ID'] = entity.id;
                parameters['PRODUCT_QUANTITY_VALUE'] = dynamic.quantity.get();
                parameters['PRODUCT_QUANTITY_MIN'] = entity.quantity.ratio;
                parameters['PRODUCT_QUANTITY_MAX'] = entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false;
                parameters['PRODUCT_QUANTITY_STEP'] = entity.quantity.ratio;

                universe.components.show({
                    'component': 'intec.universe:catalog.delivery',
                    'template': template,
                    'parameters': parameters,
                    'settings': {
                        'parameters': {
                            'width': null
                        }
                    }
                });
            });
            <?php } ?>

            if (dynamic.panel.length === 1) (function () {
                var state = false;
                var area = $(window);
                var update;
                var panel;

                update = function () {
                    var bound = 0;

                    if (root.is(':visible')) {
                        bound += root.offset().top;
                    }

                    if (area.scrollTop() > bound) {
                        panel.show();
                    } else {
                        panel.hide();
                    }
                };

                panel = dynamic.panel;
                panel.css({
                    'top': -panel.height()
                });

                panel.show = function () {
                    if (state) return;

                    state = true;
                    panel.css({
                        'display': 'block'
                    });

                    panel.trigger('show');
                    panel.stop().animate({
                        'top': 0
                    }, 500)
                };

                panel.hide = function () {
                    if (!state) return;

                    state = false;
                    panel.stop().animate({
                        'top': -panel.height()
                    }, 500, function () {
                        panel.trigger('hide');
                        panel.css({
                            'display': 'none'
                        })
                    })
                };

                update();

                area.on('scroll', update)
                    .on('resize', update);
            })();

            if (dynamic.panelMobile.length === 1 && dynamic.purchase.length === 1) (function () {
                var state = false;
                var area = $(window);
                var update;
                var panel;

                update = function () {
                    var bound = dynamic.purchase.offset().top;

                    if (area.scrollTop() > bound) {
                        panel.show();
                    } else {
                        panel.hide();
                    }
                };

                panel = dynamic.panelMobile;
                panel.css({
                    'bottom': -panel.outerHeight()
                });

                panel.show = function () {
                    if (state) return;

                    state = true;
                    panel.css({
                        'display': 'block'
                    });

                    panel.trigger('show');
                    panel.stop().animate({
                        'bottom': 0
                    }, 500)
                };

                panel.hide = function () {
                    if (!state) return;

                    state = false;
                    panel.stop().animate({
                        'bottom': -panel.outerHeight()
                    }, 500, function () {
                        panel.trigger('hide');
                        panel.css({
                            'display': 'none'
                        })
                    })
                };

                update();

                area.on('scroll', update)
                    .on('resize', update);
            })();

            <?php if (!empty($arResult['OFFERS']) && $arResult['SKU_VIEW'] == 'list') { ?>
            dynamic.offers = $('[data-role="offers"]', dynamic);
            dynamic.offers.list = $('[data-role="offer"]', dynamic.offers);

            dynamic.offers.list.each(function () {

                var offer = $(this);
                var dataOffer = offer.data('offer-data');

                offer.counter = $('[data-role="counter"]', offer);
                offer.quantity = api.controls.numeric({
                    'bounds': {
                        'minimum': dataOffer.quantity.ratio,
                        'maximum': dataOffer.quantity.trace && !dataOffer.quantity.zero ? dataOffer.quantity.value : false
                    },
                    'step': dataOffer.quantity.ratio
                }, offer.counter);

                offer.price = $('[data-role="price"]', offer);
                offer.price.base = $('[data-role="price.base"]', offer.price);
                offer.price.discount = $('[data-role="price.discount"]', offer.price);
                offer.price.difference = $('[data-role="price.difference"]', offer.price);

                offer.update = function () {

                    var price = null;

                    api.each(dataOffer.prices, function (index, object) {
                        if (object.quantity.from === null || offer.quantity.get() >= object.quantity.from)
                            price = object;
                    });

                    if (price !== null) {
                        <?php if ($bBase && $arVisual['PRICE']['RECALCULATION']) { ?>
                        if (price.quantity.from === null && price.quantity.to === null) {
                            var summary = [];

                            summary.base = price.base.value * offer.quantity.get();
                            summary.discount = price.discount.value * offer.quantity.get();
                            summary.difference = summary.base - summary.discount;
                            BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                            price.base.display = BX.Currency.currencyFormat(summary.base, price.currency.CURRENCY, true);
                            price.discount.display = BX.Currency.currencyFormat(summary.discount, price.currency.CURRENCY, true);
                            price.discount.difference = BX.Currency.currencyFormat(summary.difference, price.currency.CURRENCY, true);
                        }
                        <?php } ?>

                        offer.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                        offer.price.base.html(price.base.display);
                        offer.price.discount.html(price.discount.display);
                        offer.price.difference.html(price.discount.difference);
                    } else {
                        offer.price.attr('data-discount', 'false');
                        offer.price.base.html(null);
                        offer.price.discount.html(null);
                        offer.price.difference.html(null);
                    }

                    offer.find('[data-basket-id]')
                        .data('basketQuantity', offer.quantity.get())
                        .attr('data-basket-quantity', offer.quantity.get());
                }

                offer.quantity.on('change', function (event, value) {
                    offer.update();
                });

                <?php if ($arResult['ORDER_FAST']['USE']) { ?>

                offer.orderFast = $('[data-role="orderFast"]', offer);

                offer.orderFast.on('click', function () {
                    var template = <?= JavaScript::toObject($arResult['ORDER_FAST']['TEMPLATE']) ?>;
                    var parameters = <?= JavaScript::toObject($arResult['ORDER_FAST']['PARAMETERS']) ?>;

                    if (intec.isNull(parameters))
                        parameters = {};

                    parameters['PRODUCT'] = dataOffer.id;
                    parameters['QUANTITY'] = offer.quantity.get();

                    universe.components.show({
                        'component': 'intec.universe:sale.order.fast',
                        'template': template,
                        'parameters': parameters,
                        'settings': {
                            'parameters': {
                                'width': null
                            }
                        }
                    });
                });
                <?php } ?>

                <?php if ($arResult['DELIVERY_CALCULATION']['USE']) { ?>
                offer.deliveryCalculation = $('[data-role="deliveryCalculation"]', offer);
                offer.deliveryCalculation.on('click', function () {
                    var template = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['TEMPLATE']) ?>;
                    var parameters = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['PARAMETERS']) ?>;

                    if (intec.isNull(parameters))
                        parameters = {};

                    parameters['PRODUCT_ID'] = dataOffer.id;
                    parameters['PRODUCT_QUANTITY_VALUE'] = offer.quantity.get();
                    parameters['PRODUCT_QUANTITY_MIN'] = dataOffer.quantity.ratio;
                    parameters['PRODUCT_QUANTITY_MAX'] = dataOffer.quantity.trace && !dataOffer.quantity.zero ? dataOffer.quantity.value : false;
                    parameters['PRODUCT_QUANTITY_STEP'] = dataOffer.quantity.ratio;

                    universe.components.show({
                        'component': 'intec.universe:catalog.delivery',
                        'template': template,
                        'parameters': parameters,
                        'settings': {
                            'parameters': {
                                'width': null
                            }
                        }
                    });
                });
                <?php } ?>
            });
            <?php } ?>

            <?php if ($arVisual['PRINT']['SHOW']) { ?>
            var print = $('[data-role="print"]', dynamic);

            print.on('click', function () {
                window.print();
            });
            <?php } ?>
        });
    })(jQuery, intec);
</script>