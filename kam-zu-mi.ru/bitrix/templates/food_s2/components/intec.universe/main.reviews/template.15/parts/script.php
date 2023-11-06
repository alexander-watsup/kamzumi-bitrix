<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSettings = [
    'items' => 5,
    'loop' => $arVisual['SLIDER']['LOOP'],
    'nav' => false,
    'autoplay' => $arVisual['SLIDER']['AUTO']['USE'],
    'autoplayTimeout' => $arVisual['SLIDER']['AUTO']['TIME'],
    'autoplayHoverPause' => $arVisual['SLIDER']['AUTO']['HOVER'],
    'autoHeight' => !defined('EDITOR') ? true : false
];

?>
<script type="text/javascript">
    (function () {
        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        var container = $('[data-role="container"]', root);
        var settings = <?= JavaScript::toObject($arSettings) ?>;

        var showContent = function() {
            var content = $('.widget-owl-item.center .widget-item-content', container).html();
            var sliderContent = $('.widget-slide-content');

            sliderContent.css('opacity', 1);
            sliderContent.html(content);
        };
        var hideContent = function() {
            $('.widget-slide-content').css('opacity', 0);
        };

        container.owlCarousel({
            'items': settings.items,
            'itemClass': 'widget-owl-item owl-item',
            'loop': settings.loop,
            'center': true,
            'dots': false,
            'stageOuterClass': 'owl-stage-outer widget-outer-items',
            'stageClass': 'owl-stage',
            'nav': settings.nav,
            'autoplay': settings.autoplay,
            'autoplayTimeout': settings.autoplayTimeout,
            'autoplayHoverPause': settings.autoplayHoverPause,
            'autoHeight': settings.autoHeight,
            'onInitialized': showContent,
            'onTranslated': showContent,
            'onTranslate': hideContent,
            responsive:{
                0: {
                    'items': 1
                },
                400: {
                    'items': 3
                },
                690:{}
            }
        });

        $('.widget-button-right').click(function() {
            container.trigger('next.owl.carousel');
        });

        $('.widget-button-left').click(function() {
            container.trigger('prev.owl.carousel');
        });
    })();
</script>