<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

$arSettings = [
    'items' => 2,
    'loop' => $arVisual['SLIDER']['LOOP'],
    'nav' => true,
    'navText' => [
        '<i class="far fa-angle-left"></i>',
        '<i class="far fa-angle-right"></i>'
    ],
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

        var itemClass = (function(){
            if ($(window).width() < '601')
                return 'owl-item widget-owl-item';

            return 'owl-item intec-grid widget-owl-item';
        }());

        container.owlCarousel({
            'items': settings.items,
            'itemClass': itemClass,
            'loop': settings.loop,
            'dots': false,
            'stageOuterClass': 'owl-stage-outer widget-outer-items',
            'stageClass': 'owl-stage intec-grid',
            'nav': settings.nav,
            'navText': settings.navText,
            'navContainerClass': 'intec-ui intec-ui-control-navigation',
            'navClass': ['intec-ui-part-button-left intec-cl-background-hover intec-cl-border-hover', 'intec-ui-part-button-right intec-cl-background-hover intec-cl-border-hover'],
            'autoplay': settings.autoplay,
            'autoplayTimeout': settings.autoplayTimeout,
            'autoplayHoverPause': settings.autoplayHoverPause,
            'autoHeight': settings.autoHeight,
            responsive:{
                0: {
                    'items': 1,
                    'dots': true,
                    'dotClass': 'owl-dot widget-items-dot intec-grid-item-auto intec-cl-background intec-cl-border',
                    'dotsClass': 'owl-dots widget-items-dots intec-grid intec-grid-a-h-center',
                    'nav': false,
                    'stageClass': 'owl-stage',
                    'stageOuterClass': 'owl-stage-outer'
                },
                690:{}
            }
        });
    })();
</script>