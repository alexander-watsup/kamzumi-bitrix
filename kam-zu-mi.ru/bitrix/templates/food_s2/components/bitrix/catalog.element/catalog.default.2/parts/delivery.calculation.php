<?php if (defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true);

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;

?>
<div class="catalog-element-delivery-calculation-button-wrap">
    <div class="catalog-element-delivery-calculation-button intec-button intec-button-link intec-button-w-icon" data-role="deliveryCalculation">
        <i class="intec-button-icon">
            <?= FileHelper::getFileData(__DIR__.'/../svg/delivery.icon.svg')?>
        </i>
        <div class="catalog-element-delivery-calculation-text intec-button-text intec-cl-text-hover">
            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_PURCHASE_DELIVERY_CALCULATION') ?>
        </div>
    </div>
</div>