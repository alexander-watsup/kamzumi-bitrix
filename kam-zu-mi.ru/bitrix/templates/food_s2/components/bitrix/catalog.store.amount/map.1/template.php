<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arParams['MAP_TYPE'] = 'google';
$arVisual = $arResult['VISUAL'];
$arSvg = [
    'FULL' => [
        'PHONE' => FileHelper::getFileData(__DIR__.'/svg/full.item.phone.svg'),
        'EMAIL' => FileHelper::getFileData(__DIR__.'/svg/full.item.email.svg'),
        'CLOSE' => FileHelper::getFileData(__DIR__.'/svg/full.item.close.svg')
    ]
];

$vQuantity = include(__DIR__.'/parts/quantity.php');

?>
<div class="ns-bitrix c-catalog-store-amount c-catalog-store-amount-map-1" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="store-amount-content">
                <div class="store-amount-list store-amount-part">
                    <?php include(__DIR__.'/parts/list.php') ?>
                    <?php include(__DIR__.'/parts/full.php') ?>
                </div>
                <div class="store-amount-map store-amount-part">
                    <?php include(__DIR__.'/parts/map.php') ?>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).on('ready', function () {
            let component = new intecCatalogStoreOffersMap(
                <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                <?= JavaScript::toObject($arResult['JS']) ?>
            );

            if (window.offers) {
                let offer = window.offers.getCurrent();

                window.offers.on('change', function (event, offer, values) {
                    component.offerOnChange(offer.id);
                });

                if (offer)
                    component.offerOnChange(offer.id);
            }

            let root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
            let scroll = $('[data-scroll]', root);
            let full = $('[data-role="store.full"]', root);
            let list = $('[data-role="store.list.item"]', root);

            scroll.scrollbar();

            full.items = $('[data-role="store.full.item"]', full);
            full.items.each(function () {
                let self = $(this);
                let id = self.attr('data-store-id');
                let close = $('[data-role="store.full.item.close"]', self);

                close.on('click', function () {
                    self.attr('data-active', 'false');
                    full.attr('data-active', 'false');
                });
            });

            list.eq(0).attr('data-active', 'true');
            list.on('click', function () {
                let self = $(this);
                let id = self.attr('data-store-id');

                component.jump(id);

                if (self.attr('data-active') === 'false') {
                    list.attr('data-active', 'false');
                    self.attr('data-active', 'true');
                }

                full.attr('data-active', 'true');
                full.items.filter('[data-store-id="' + id + '"]').attr('data-active', 'true');
            });
        });
    </script>
</div>