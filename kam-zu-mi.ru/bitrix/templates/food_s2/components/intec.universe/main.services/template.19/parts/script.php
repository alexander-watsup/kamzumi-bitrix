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

?>
<script type="text/javascript">
    (function ($, api) {
        $(function () {
            let root = $(<?= JavaScript::toObject('#' . $sTemplateId) ?>);
            let tabs = $('[data-role="section.tabs"]', root.section);
            let content = $('[data-role="section.content"]', root.section);

            tabs.items = $('[data-role="section.tabs.item"]', tabs);
            content.items = $('[data-role="section.content.item"]', content);

            tabs.items.on('click', function () {
                let self = $(this);
                let active = self.attr('data-active') === 'true';
                let id = self.attr('data-id');

                if (!active) {
                    tabs.items.filter('[data-active="true"]')
                        .attr('data-active', 'false')
                        .removeClass('intec-cl-border');

                    self.attr('data-active', 'true')
                        .addClass('intec-cl-border');

                    content.items.filter('[data-active="true"]')
                        .attr('data-active', 'false');

                    content.items.filter('[data-id="' + id + '"]')
                        .attr('data-active', 'processing');

                    setTimeout(function () {
                        content.items.filter('[data-id="' + id + '"]')
                            .attr('data-active', 'true');
                    }, 5);
                }
            });

        });
    })(jQuery, intec);
</script>