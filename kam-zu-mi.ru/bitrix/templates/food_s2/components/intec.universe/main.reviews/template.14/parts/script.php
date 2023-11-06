<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 */

if ($arVisual['SLIDER']['USE']) {
    $arSettings = [
        'items' => 1,
        'dots' => true,
    ];
}

?>
<script type="text/javascript">
    (function () {
        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        var container = $('[data-role="container"]', root);

        <?php if ($arVisual['VIDEO']['SHOW']) { ?>
            container.lightGallery({
                'selector': '[data-role="video"]'
            });
        <?php } ?>
        <?php if ($arVisual['SLIDER']['USE']) { ?>
            var settings = <?= JavaScript::toObject($arSettings) ?>;

            container.owlCarousel({
                'items': settings.items,
                'dots': settings.dots,
                'margin': 15,
                'dotsContainer': <?= JavaScript::toObject('#'.$sTemplateId) ?>+' .widget-items-dots',
                'autoHeight': true
            });

            $('.widget-items-dot', root).click(function(){
                container.trigger('to.owl.carousel', [$(this).index(), 300]);
            });
        <?php } ?>
    })();
</script>