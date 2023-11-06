<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$iCounter = 0;

?>
<div class="catalog-element-properties-preview">
    <div class="catalog-element-properties-preview-name">
        <?php if (!empty($arVisual['PROPERTIES']['PREVIEW']['NAME'])) { ?>
            <?= $arVisual['PROPERTIES']['PREVIEW']['NAME'] ?>
        <?php } else { ?>
            <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PROPERTIES_PREVIEW_NAME') ?>
        <?php } ?>
    </div>
    <div class="catalog-element-properties-preview-item-container">
        <?php foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty) {

            if (empty($arProperty['DISPLAY_VALUE']))
                continue;

            $iCounter++;

            if ($iCounter > $arVisual['PROPERTIES']['PREVIEW']['COUNT'])
                break;

            if (Type::isArray($arProperty['DISPLAY_VALUE']))
                $arProperty['DISPLAY_VALUE'] = implode(', ', $arProperty['DISPLAY_VALUE']);
            else
                $arProperty['DISPLAY_VALUE'];

        ?>
            <div class="catalog-element-properties-preview-item">
                <?= Html::tag('span', $arProperty['NAME'], [
                    'class' => 'catalog-element-properties-preview-item-name'
                ]) ?>
                <?= Html::tag('span', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PROPERTIES_PREVIEW_ITEM_SEPARATOR'), [
                    'class' => 'catalog-element-properties-preview-item-separator'
                ]) ?>
                <?= Html::tag('span', $arProperty['DISPLAY_VALUE'], [
                    'class' => 'catalog-element-properties-preview-item-value'
                ]) ?>
            </div>
        <?php } ?>
        <?php unset($arProperty) ?>
    </div>
</div>
<?php unset($iCounter) ?>