<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var array $arVisual
 * @var array $arResponsiveReady
 * @var string $sTemplateId
 */
?>
<script>
    (function ($, api) {
        var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
        var gallery = $('[data-entity="gallery"]', root);

        <?php if (!defined('EDITOR')) { ?>
        gallery.lightGallery({
            'selector': '[data-play="true"]'
        });
        <?php } ?>
    })(jQuery, intec);
</script>