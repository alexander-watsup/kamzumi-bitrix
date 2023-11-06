<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 */

?>
<?php if (!empty($arResult['ELEMENTS'])) { ?>
    <?php foreach ($arResult['ELEMENTS'] as $arItem) { ?>
        <div class="widget-item">
            <div class="widget-item-content">
                <div class="intec-grid intec-grid-nowrap intec-grid-i-h-8 intec-grid-i-v-6 intec-grid-500-wrap">
                    <div class="intec-grid-item-auto intec-grid-item-500-1">
                        <div class="widget-item-picture">
                            <?= $arSvg['AVATAR'] ?>
                            <div class="intec-aligner"></div>
                        </div>
                    </div>
                    <div class="intec-grid-item intec-grid-item-500-1">
                        <div class="widget-item-name">
                            <?= $arItem['NAME'] ?>
                        </div>
                        <div class="widget-item-description">
                            <?= $arItem['PREVIEW_TEXT'] ?>
                        </div>
                        <?php if ($arVisual['DATE']['SHOW']) { ?>
                            <div class="widget-item-date">
                                <?= $arItem['DATA']['DATE'] ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="widget-item">
        <div class="widget-item-content widget-item-content-empty">
            <?= Loc::getMessage('C_REVIEWS_TEMPLATE_2_TEMPLATE_CONTENT_EMPTY') ?>
        </div>
    </div>
<?php } ?>