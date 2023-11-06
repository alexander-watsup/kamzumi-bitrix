<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Type;

/**
 * @var array $arResult
 */

?>
<div class="catalog-element-properties-detail">
    <?php foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) { ?>
        <div class="catalog-element-properties-detail-item">
            <div class="intec-grid intec-grid-a-v-center intec-grid-i-4 intec-grid-500-wrap">
                <div class="intec-grid-item-2 intec-grid-item-500-1">
                    <div class="catalog-element-properties-detail-item-name">
                        <?= $arProperty['NAME'] ?>
                    </div>
                </div>
                <div class="intec-grid-item-2 intec-grid-item-500-1">
                    <div class="catalog-element-properties-detail-item-value">
                        <?php if (Type::isArray($arProperty['DISPLAY_VALUE'])) { ?>
                            <?= implode(', ', $arProperty['DISPLAY_VALUE']) ?>
                        <?php } else { ?>
                            <?= $arProperty['DISPLAY_VALUE'] ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php unset($arProperty) ?>