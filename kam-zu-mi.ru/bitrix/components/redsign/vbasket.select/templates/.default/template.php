<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;

define('VUEJS_DEBUG', true);

\Bitrix\Main\UI\Extension::load('ui.vue');
\Bitrix\Main\UI\Extension::load("ui.notification");

$this->addExternalCss($templateFolder.'/js/component.css');
$this->addExternalJS($templateFolder.'/js/component.js');

$sBlockId = 'vbasket_'.$this->randString(5);
$messages = Loc::loadLanguageFile(__FILE__);

$colors = [
	"#5867DD","#D32F2F","#C2185B","#7B1FA2","#512DA8",
	"#303F9F","#1976D2","#0288D1","#0097A7","#00796B",
	"#388E3C","#689F38","#AFB42B","#FBC02D","#FFA000",
	"#F57C00","#E64A19","#5D4037","#616161","#455A64"
];
$defaultColor = "#5867DD";
?>

<div class="vbasket-select" id="<?=$sBlockId;?>"></div>

<script>
	BX.message(<?=CUtil::PhpToJSObject($messages)?>);

	new VBasket.Components.Select(
		document.getElementById('<?=$sBlockId?>'),
		<?=\Bitrix\Main\Web\Json::encode($arResult)?>,
		{
			colors: <?=\Bitrix\Main\Web\Json::encode($colors)?>,
			defaultColor: '<?=CUtil::JSEscape($defaultColor)?>'
		}
	);

</script>