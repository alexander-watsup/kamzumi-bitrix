<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

$arSlider = [
    'items' => 1,
    'nav' => $arVisual['SLIDER']['NAV'],
    'navText' => [
        '<i class="fal fa-angle-left"></i>',
        '<i class="fal fa-angle-right"></i>'
    ],
    'dots' => false
];

?>
<script type="text/javascript">
    (function ($, api) {
        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        var gallery = $('[data-role="items"]', root);
        var settings = <?= JavaScript::toObject($arSlider) ?>;

        gallery.owlCarousel({
            'items': settings.items,
            'nav': settings.nav,
            'navText': settings.navText,
            'dots': false,
            'autoHeight': true
        });

    })(jQuery, intec);
</script>