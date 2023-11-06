<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="vacancyItem" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="vacancyNameBlock">
			
			<div class="NameDescription">
				<div class="name"><?=$arItem["NAME"]?></div>
				<div class="description">
					<?foreach($arItem["DISPLAY_PROPERTIES"] as $arProperty):
						if($arProperty["VALUE"]!=""):
						?>
						<span><?=$arProperty["VALUE"];?></span>
						<?
						endif;
					endforeach;?>
				</div>
			</div>
			<div class="price">
				<?=$arItem["PROPERTIES"]["PAY"]["VALUE"];?>
				<span class="arrow"></span>
			</div>
		</div>
		<div class="description">
			<?=$arItem["DETAIL_TEXT"]?>
			<a class="send" href="/contacts/">Связаться с нами</a>
		</div>
	</div>
<?endforeach;?>
<?
// ppr($arResult);
?>

