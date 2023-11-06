<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var string $sTemplateId
 * @var bool $bOffers
 * @var bool $bSkuDynamic
 * @var bool $bSkuList
 */

$bBase = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;

if ($bBase)
    CJSCore::Init(['currency']);

?>
<script type="text/javascript">
    (function ($, api) {
        $(function () {
            let area = $(document);
            let root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
            let dynamic = $('[data-role="dynamic"]', root);
            let properties = root.data('properties');
            let data = root.data('data');
            let entity = data;

            dynamic.offers = new universe.catalog.offers({
                'properties': properties,
                'list': data.offers
            });

            window.offers = dynamic.offers;

            root.gallery = $('[data-role="gallery"]', root);
            root.gallery.preview = $('[data-role="gallery.preview"]', root.gallery);
            root.panel = $('[data-role="panel"]', root);
            root.panel.picture = $('[data-role="panel.picture"]', root.panel);
            root.section = $('[data-role="section"]', root);
            root.anchors = $('[data-role="anchor"]', root);
            root.storeSection = $('[data-role="store.section"]', root);

            dynamic.recalculation = dynamic.data('recalculation');
            dynamic.summary = $('[data-role="item.summary"]', dynamic);
            dynamic.summary.price = $('[data-role="item.summary.price"]', dynamic.summary);
            dynamic.article = $('[data-role="article"]', dynamic);
            dynamic.article.value = $('[data-role="article.value"]', dynamic.article);
            dynamic.counter = $('[data-role="counter"]', dynamic);
            dynamic.quantity = api.controls.numeric({
                'bounds': {
                    'minimum': entity.quantity.ratio,
                    'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                },
                'step': entity.quantity.ratio
            }, dynamic.counter);
            dynamic.price = $('[data-role="price"]', dynamic);
            dynamic.price.base = $('[data-role="price.base"]', dynamic.price);
            dynamic.price.discount = $('[data-role="price.discount"]', dynamic.price);
            dynamic.price.percent = $('[data-role="price.percent"]', dynamic.price);
            dynamic.price.difference = $('[data-role="price.difference"]', dynamic.price);
            dynamic.price.measure = $('[data-role="price.measure"]', dynamic. price);

            dynamic.panelMobile = $('[data-role="panel.mobile"]', dynamic);
            dynamic.purchase = $('[data-role="purchase"]', dynamic);

            <?php if (!$bOffers || $bSkuDynamic) { ?>
                dynamic.update = function () {
                    let article = entity.article;
                    let price = null;
                    let quantity = {
                        'bounds': {
                            'minimum': entity.quantity.ratio,
                            'maximum': entity.quantity.trace && !entity.quantity.zero ? entity.quantity.value : false
                        },
                        'step': entity.quantity.ratio
                    };

                    root.attr('data-available', entity.available ? 'true' : 'false');

                    if (article == null)
                        article = data.article;

                    dynamic.article.attr('data-show', article == null ? 'false' : 'true');
                    dynamic.article.value.text(article);

                    api.each(entity.prices, function (index, object) {
                        if (object.quantity.from === null || dynamic.quantity.get() >= object.quantity.from)
                            price = object;
                    });

                    if (price !== null) {
                        if (dynamic.recalculation == true) {
                            let summary = [];

                            summary.value = price.discount.value * dynamic.quantity.get();
                            summary.value = summary.value.toFixed(price.currency.DECIMALS);

                            BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                            summary.display = BX.Currency.currencyFormat(summary.value, price.currency.CURRENCY, true);

                            dynamic.summary.price.html(summary.display);
                        }

                        dynamic.price.attr('data-discount', price.discount.use ? 'true' : 'false');
                        dynamic.price.base.html(price.base.display);
                        dynamic.price.discount.html(price.discount.display);
                        dynamic.price.percent.text('-' + price.discount.percent + '%');
                        dynamic.price.difference.html(price.discount.difference);

                        if (entity.quantity.measure) {
                            dynamic.price.attr('data-measure', 'true');
                            dynamic.price.measure.html(entity.quantity.measure);
                        } else {
                            dynamic.price.attr('data-measure', 'false');
                            dynamic.price.measure.html(null);
                        }
                    } else {
                        dynamic.price.attr('data-discount', 'false');
                        dynamic.price.base.html(null);
                        dynamic.price.discount.html(null);
                        dynamic.price.percent.text(null);
                        dynamic.price.difference.html(null);
                        dynamic.price.measure.html(null);
                    }

                    dynamic.price.attr('data-show', price !== null ? 'true' : 'false');
                    dynamic.quantity.configure(quantity);

                    dynamic.find('[data-offer]').css('display', '');

                    if (entity !== data) {
                        dynamic.find('[data-offer=' + entity.id + ']').css('display', 'block');
                        dynamic.find('[data-offer="false"]').css('display', 'none');

                        if (root.gallery.filter('[data-offer=' + entity.id + ']').length === 0)
                            root.gallery.filter('[data-offer="false"]').css('display', '');

                        if (root.panel.picture.filter('[data-offer=' + entity.id + ']').length === 0)
                            root.panel.picture.filter('[data-offer="false"]').css('display', '');
                    }

                    dynamic.find('[data-basket-id]')
                        .data('basketQuantity', dynamic.quantity.get())
                        .attr('data-basket-quantity', dynamic.quantity.get());

                    if (dynamic.summary.length !== 0) {
                        if (dynamic.quantity.get() === 1) {
                            if (!dynamic.summary.activated) {

                                dynamic.summary.animate({'height': 0}, 500, function () {
                                    dynamic.summary.css({
                                        'height': '',
                                        'display': ''
                                    });
                                });

                                dynamic.summary.addClass('hidden');
                                dynamic.attr('data-recalculation', 'false');
                            }
                            dynamic.summary.activated = true;
                        } else if (dynamic.quantity.get() > 0) {
                            let heightSummary;
                            let recalc = dynamic.attr('data-recalculation');

                            dynamic.summary.removeClass('hidden');

                            if (recalc == 'false') {
                                dynamic.summary.css({
                                    'height': 'auto',
                                    'display': 'block'
                                });
                                heightSummary = dynamic.summary.outerHeight();
                                dynamic.summary.css('height', 0);

                                dynamic.summary.animate({'height': heightSummary}, 500, function () {
                                    dynamic.summary.css('top', {
                                        'height': '',
                                        'display': ''
                                    });
                                });
                            }

                            dynamic.attr('data-recalculation', 'true');
                            dynamic.summary.activated = true;
                        }
                    }
                };

                dynamic.update();

                (function () {
                    let update = false;

                    dynamic.quantity.on('change', function () {
                        if (!update) {
                            update = true;
                            dynamic.update();
                            update = false;
                        }
                    });
                })();

                <?php if ($arResult['DELIVERY_CALCULATION']['USE']) { ?>
                    dynamic.deliveryCalculation = $('[data-role="deliveryCalculation"]', dynamic);
                    dynamic.deliveryCalculation.on('click', function () {
                        let template = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['TEMPLATE']) ?>;
                        let parameters = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['PARAMETERS']) ?>;

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

                <?php if ($arResult['ORDER_FAST']['USE']) { ?>
                    dynamic.orderFast = $('[data-role="orderFast"]', dynamic);
                    dynamic.orderFast.on('click', function () {
                        let template = <?= JavaScript::toObject($arResult['ORDER_FAST']['TEMPLATE']) ?>;
                        let parameters = <?= JavaScript::toObject($arResult['ORDER_FAST']['PARAMETERS']) ?>;

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

                <?php if ($arVisual['PANEL']['DESKTOP']['SHOW']) { ?>
                    if (root.panel.length) (function () {
                        let state = false;
                        let area = $(window);
                        let update;
                        let panel = root.panel;
                        let sticky = {
                            'top': $('[data-sticky="top"]', root),
                            'nulled': $('[data-sticky="nulled"]', root)
                        };

                        panel.css('top', -panel.outerHeight());

                        update = function () {
                            let bound = 0;

                            if (root.is(':visible'))
                                bound += root.offset().top;

                            if (area.width() <= 768) {
                                sticky.top.css('top', '');
                                sticky.nulled.css('top', '');
                            }

                            if (area.scrollTop() > bound)
                                panel.show();
                            else
                                panel.hide();
                        };

                        panel.show = function () {
                            if (state) return;

                            state = true;

                            panel.css('display', 'block')
                                .trigger('show')
                                .stop()
                                .animate({'top': 0}, 500);

                            if (area.width() > 768) {
                                if (sticky.top.length)
                                    sticky.top.animate({'top': panel.outerHeight() + 16}, 500);

                                if (sticky.nulled.length)
                                    sticky.nulled.animate({'top': panel.outerHeight() - 1}, 500);
                            }
                        };

                        panel.hide = function () {
                            if (!state) return;

                            state = false;

                            panel.stop().animate({
                                'top': -panel.height()
                            }, 500, function () {
                                panel.trigger('hide');
                                panel.css('display', 'none');
                            });

                            if (area.width() > 768) {
                                if (sticky.top.length) {
                                    sticky.top.animate({
                                        'top': 16
                                    }, 500, function () {
                                        sticky.top.css('top', '');
                                    });
                                }

                                if (sticky.nulled.length) {
                                    sticky.nulled.animate({
                                        'top': -1
                                    }, 500, function () {
                                        sticky.nulled.css('top', '');
                                    });
                                }
                            }
                        };

                        update();

                        area.on('scroll', update)
                            .on('resize', update);
                    })();
                <?php } ?>

                <?php if ($arVisual['PANEL']['MOBILE']['SHOW']) { ?>
                    if (dynamic.panelMobile.length === 1 && dynamic.purchase.length === 1) (function () {
                        let state = false;
                        let area = $(window);
                        let update;
                        let panel;

                        update = function () {
                            let bound = dynamic.purchase.offset().top;

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
                <?php } ?>
            <?php } ?>

            <?php if ($bSkuDynamic) { ?>
                if (!dynamic.offers.isEmpty()) {
                    dynamic.properties = $('[data-role="property"]', dynamic);
                    dynamic.properties.values = $('[data-role="property.value"]', dynamic.properties);
                    dynamic.properties.each(function () {
                        let self = $(this);
                        let property = self.data('property');
                        let values = self.find(dynamic.properties.values);

                        values.each(function () {
                            let self = $(this);
                            let value = self.data('value');

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

                                let selected = $('[data-role="property.selected"]', property);

                                api.each(values, function (index, value) {
                                    value = property.find(dynamic.properties.values).filter('[data-value="' + value + '"]');
                                    value.attr('data-state', state);

                                    let valueContent = $('[data-role="property.value.content"]', value);
                                    let valueContentType = valueContent.attr('data-content-type');

                                    if (state === 'selected') {
                                        selected.html(valueContent.attr('title'));

                                        if (valueContentType === 'text') {
                                            valueContent.removeClass('intec-cl-border-hover intec-cl-background-hover')
                                                .addClass('intec-cl-background intec-cl-border intec-cl-background-light-hover intec-cl-border-light-hover');
                                        } else if (valueContentType === 'picture') {
                                            valueContent.removeClass('intec-cl-border-hover')
                                                .addClass('intec-cl-border intec-cl-border-light-hover');
                                        }
                                    } else {
                                        if (valueContentType === 'text') {
                                            valueContent.addClass('intec-cl-border-hover intec-cl-background-hover')
                                                .removeClass('intec-cl-background intec-cl-border intec-cl-background-light-hover intec-cl-border-light-hover');
                                        } else if (valueContentType === 'picture') {
                                            valueContent.removeClass('intec-cl-border intec-cl-border-light-hover')
                                                .addClass('intec-cl-border-hover');
                                        }
                                    }
                                });
                            });
                        });

                        dynamic.update();
                    });

                    let offerCurrent;

                    api.each(dynamic.offers.getList(), function (index, offer) {
                        if (!offerCurrent || offerCurrent.sort > offer.sort)
                            offerCurrent = offer;
                    });

                    dynamic.offers.setCurrentById(offerCurrent.id);
                }
            <?php } ?>

            <?php if ($bSkuList) { ?>
                dynamic.offers = $('[data-role="offers"]', root);
                dynamic.offers.list = $('[data-role="offer"]', dynamic.offers);

                dynamic.offers.list.each(function () {
                    let offer = $(this);
                    let dataOffer = offer.data('offer-data');

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
                        let price = null;

                        api.each(dataOffer.prices, function (index, object) {
                            if (object.quantity.from === null || offer.quantity.get() >= object.quantity.from)
                                price = object;
                        });

                        if (price !== null) {
                            <?php if ($bBase && $arVisual['PRICE']['RECALCULATION']) { ?>
                                if (price.quantity.from === null && price.quantity.to === null) {
                                    let summary = [];

                                    summary.base = price.base.value * offer.quantity.get();
                                    summary.discount = price.discount.value * offer.quantity.get();
                                    BX.Currency.setCurrencyFormat(price.currency.CURRENCY, price.currency);
                                    price.base.display = BX.Currency.currencyFormat(summary.base, price.currency.CURRENCY, true);
                                    price.discount.display = BX.Currency.currencyFormat(summary.discount, price.currency.CURRENCY, true);
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
                    };

                    offer.quantity.on('change', function (event, value) {
                        offer.update();
                    });

                    <?php if ($arResult['ORDER_FAST']['USE']) { ?>
                        offer.orderFast = $('[data-role="orderFast"]', offer);

                        offer.orderFast.on('click', function () {
                            let template = <?= JavaScript::toObject($arResult['ORDER_FAST']['TEMPLATE']) ?>;
                            let parameters = <?= JavaScript::toObject($arResult['ORDER_FAST']['PARAMETERS']) ?>;

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
                            let template = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['TEMPLATE']) ?>;
                            let parameters = <?= JavaScript::toObject($arResult['DELIVERY_CALCULATION']['PARAMETERS']) ?>;

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

                dynamic.offers.list.on('mouseenter', function () {
                    let self = $(this);

                    if (area.width() < 1025)
                        self.css('height', '');
                    else
                        self.css('height', this.getBoundingClientRect().height);

                    self.attr('data-expanded', 'true');
                });

                dynamic.offers.list.on('mouseleave', function () {
                    let self = $(this);

                    self.css('height', '');

                    if (area.width() > 1024)
                        self.attr('data-expanded', 'false');
                    else
                        self.attr('data-expanded', 'true');
                });

                let adaptOffersList = function () {
                    dynamic.offers.list.css('height', '');

                    if (area.width() > 1024)
                        dynamic.offers.list.attr('data-expanded', 'false');
                    else
                        dynamic.offers.list.attr('data-expanded', 'true');
                };

                area.on('ready', adaptOffersList);
                window.addEventListener('resize', adaptOffersList);
            <?php } ?>

            root.gallery.each(function () {
                let gallery = $(this);
                let pictures = $('[data-role="gallery.pictures"]', gallery);
                let preview = $('[data-role="gallery.preview"]', gallery);

                pictures.action = pictures.data('action');
                pictures.zoom = pictures.data('zoom');
                pictures.slider = $('[data-role="gallery.pictures.slider"]', pictures);
                pictures.items = $('[data-role="gallery.pictures.item"]', pictures);

                preview.slider = $('[data-role="gallery.preview.slider"]', preview);
                preview.items = $('[data-role="gallery.preview.slider.item"]', preview);
                preview.navigation = $('[data-role="gallery.preview.navigation"]', preview);

                pictures.slider.owlCarousel({
                    'items': 1,
                    'nav': false,
                    'dots': false
                });

                preview.slider.owlCarousel({
                    'items': 3,
                    'margin': 8,
                    'nav': true,
                    'navContainer': preview.navigation,
                    'navClass': ['preview-navigation-left', 'preview-navigation-right'],
                    'navText': [
                        <?= JavaScript::toObject($arSvg['NAVIGATION']['LEFT']) ?>,
                        <?= JavaScript::toObject($arSvg['NAVIGATION']['RIGHT']) ?>
                    ],
                    'dots': false
                });

                pictures.slider.on('dragged.owl.carousel', function (event) {
                    let index = event.item.index;

                    preview.slider.trigger('to.owl.carousel', index);
                    preview.items.attr('data-active', 'false');
                    preview.items.eq(index).attr('data-active', 'true');
                });

                if (pictures.action === 'popup') {
                    pictures.slider.lightGallery({
                        'share': false,
                        'selector': '[data-role="gallery.pictures.item.picture"]'
                    });
                }

                if (pictures.zoom) {
                    pictures.items.each(function () {
                        let self = $(this);
                        let picture = $('[data-role="gallery.pictures.item.picture"]', self);
                        let source = picture.data('src');

                        picture.zoom({
                            'url': source,
                            'touch': false
                        });
                    });
                }

                preview.items.on('click', function () {
                    let item = $(this);
                    let index = item.closest('.owl-item').index();

                    preview.items.attr('data-active', 'false');
                    item.attr('data-active', 'true');
                    pictures.slider.trigger('to.owl.carousel', index);
                });
            });

            if (root.anchors.length) {
                root.anchors.on('click', function () {
                    let self = $(this);
                    let target = $('[data-scroll-end="' + self.attr('data-scroll-to') + '"]', root);

                    $('html, body').animate({'scrollTop': target.offset().top - 16}, 400);
                });
            }

            if (root.storeSection.length) {
                let tabs = $('[data-role="store.section.tab"]', root.storeSection);
                let content = $('[data-role="store.section.content"]', root.storeSection);

                tabs.on('click', function () {
                    let self = $(this);
                    let id = self.attr('data-store-section-id')
                    let active = self.attr('data-store-section-active') === 'true';

                    if (!active) {
                        tabs.attr('data-store-section-active', 'false');
                        self.attr('data-store-section-active', 'true');

                        content.attr('data-store-section-active', 'false');
                        content.filter('[data-store-section-id="' + id + '"]')
                            .attr('data-store-section-active', 'procesing');

                        setTimeout(function () {
                            content.filter('[data-store-section-id="' + id + '"]')
                                .attr('data-store-section-active', 'true');
                        }, 1);
                    }
                });
            }

            <?php if ($arVisual['SECTIONS']) { ?>
                if (root.section.length) (function () {
                    let tabs = $('[data-role="section.tabs"]', root.section);
                    let content = $('[data-role="section.content"]', root.section);
                    let offset = root.section.offset().top + tabs.height();

                    $('[data-role="scroll"]', tabs).scrollbar();

                    tabs.items = $('[data-role="section.tabs.item"]', tabs);
                    content.items = $('[data-role="section.content.item"]', content);

                    tabs.items.on('click', function () {
                        let self = $(this);
                        let active = self.attr('data-active') === 'true';
                        let id = self.attr('data-id');

                        offset = root.section.offset().top + tabs.height();

                        if (root.panel.length && area.width() > 768)
                            offset = offset - root.panel.outerHeight();

                        if (!active) {
                            tabs.items.filter('[data-active="true"]')
                                .attr('data-active', 'false')
                                .removeClass('intec-cl-background intec-cl-background-light-hover');

                            self.attr('data-active', 'true')
                                .addClass('intec-cl-background intec-cl-background-light-hover');

                            content.items.filter('[data-active="true"]')
                                .attr('data-active', 'false');

                            content.items.filter('[data-id="' + id + '"]')
                                .attr('data-active', 'processing');

                            setTimeout(function () {
                                content.items.filter('[data-id="' + id + '"]')
                                    .attr('data-active', 'true');
                            }, 5);
                        }

                        if (area.scrollTop() > offset)
                            $('html, body').animate({'scrollTop': offset - 40}, 300);
                    });
                })();
            <?php } ?>

            <?php if ($arResult['FORM']['ORDER']['SHOW']) { ?>
                dynamic.order = $('[data-role="order"]', dynamic);
                dynamic.order.on('click', function () {
                    let options = <?= JavaScript::toObject([
                        'id' => $arResult['FORM']['ORDER']['ID'],
                        'template' => $arResult['FORM']['ORDER']['TEMPLATE'],
                        'parameters' => [
                            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                            'CONSENT_URL' => $arResult['URL']['CONSENT']
                        ],
                        'settings' => [
                            'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_ORDER_FORM_TITLE')
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
                    let options = <?= JavaScript::toObject([
                        'id' => $arResult['FORM']['CHEAPER']['ID'],
                        'template' => $arResult['FORM']['CHEAPER']['TEMPLATE'],
                        'parameters' => [
                            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                            'CONSENT_URL' => $arResult['URL']['CONSENT']
                        ],
                        'settings' => [
                            'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_CHEAPER_TITLE')
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

            <?php if ($arVisual['SECTIONS']) { ?>
                $('[data-role="properties.preview.button"]', dynamic).click(function(){
                    $('[data-role="section.tabs.item"][data-id="properties"]').trigger('click');
                });
            <?php } else { ?>
                let heightPanel = 0;

                if (root.panel.length !== 0) {
                    heightPanel = root.panel.outerHeight();
                }

                $('[data-role="properties.preview.button"]', dynamic).click(function(){
                    area.scrollTo($('[data-role="properties.detail"]', dynamic).offset().top - heightPanel, 1000);
                });
            <?php } ?>
        });
    })(jQuery, intec);
</script>