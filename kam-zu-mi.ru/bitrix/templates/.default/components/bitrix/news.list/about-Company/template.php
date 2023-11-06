<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
Loc::loadMessages(__FILE__);
?>
<div class="slider-title">Нам доверяют: </div>
<div class="slider-about">
    <?php
    foreach ($arResult['ITEMS'] as $arItem):
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    ?>
	<div class="item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<img src="<?=$arItem['PREVIEW_PICTURE']['SAFE_SRC']?>" alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" class=""/>
	</div>
    <?php endforeach; ?>
</div>

