<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}

use Bitrix\Main\Localization\Loc;
use Redsign\B2BPortal\UI\Portlet;

$this->addExternalJs(SITE_TEMPLATE_PATH.'/assets/vendors/polyfill/polyfill.js');
$this->addExternalJs(SITE_TEMPLATE_PATH.'/assets/vendors/vue/vue.js');

$this->addExternalJS($templateFolder.'/js/store.js');
$this->addExternalJS($templateFolder.'/js/component.js');

$arColumns = [
	[
		'name' => Loc::getMessage('RS_B2B_SE_COLUMN_NAME'),
		'key' => 'name'
	],
	[
		'name' => Loc::getMessage('RS_B2B_SE_COLUMN_EXTRA'),
		'key' => 'extra'
	],
	[
		'name' => Loc::getMessage('RS_B2B_SE_COLUMN_EDIT_EXTRA'),
		'key' => 'edit_extra'
	]
];

$sBlockId = 'sectionsExtraTable_'.randString(5);
?>

<div class="row">
	<div class="col-md-9">
		<?php
		$portlet = new Portlet();
		$portlet->head(new Portlet\Head(function() use($arResult, $sBlockId) {

			$this->toolbar(function() use($arResult, $sBlockId) {
				?>
				<div class="dropdown" id="<?=$sBlockId?>_prices"></div>
				<?
			});

		}));
		$portlet->body(function() use($sBlockId) {
			?><div id="<?=$sBlockId?>"></div><?php
		})->addModifier('fit'); 

		$portlet->render();
		?>
	</div>
	<div class="col-md-3">
		<?php
		Portlet::simple(Loc::getMessage('RS_B2B_SE_CHANGES_TITLE'), function () use ($sBlockId) {
			?><div id="<?=$sBlockId?>_changelist"></div><?php
		})->render();        
		?>
	</div>
</div>
<script>

BX.message({
	'RS_SECTIONS_EXTRA_CONFIRM_SET_EXTRA': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_SET_EXTRA')?>',
	'RS_SECTIONS_EXTRA_CONFIRM_SET_EXTRA_CATALOG': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_SET_EXTRA_CATALOG')?>',
	'RS_SECTIONS_EXTRA_CHANGES_EMPTY': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_CHANGES_EMPTY')?>',
	'RS_SECTIONS_EXTRA_RESET_CHANGES': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_RESET_CHANGES')?>',
	'RS_SECTIONS_EXTRA_SAVE_CHANGES': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_SAVE_CHANGES')?>',
	'RS_SECTIONS_EXTRA_ARE_YOU_SURE': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_ARE_YOU_SURE')?>',
	'RS_SECTIONS_EXTRA_CATALOG': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_CATALOG')?>',
	'RS_SECTIONS_EXTRA_SAVE_PRICES': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_SAVE_PRICES')?>',
	'RS_SECTIONS_EXTRA_ERROR': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_ERROR')?>',
	'RS_SECTIONS_EXTRA_SAVE_SUCCESS': '<?=Loc::getMessage('RS_B2B_SE_MESSAGE_SAVE_SUCCESS')?>',
});

(function () {
	
	var store = new SectionsExtraStore({
		columns: <?=\Bitrix\Main\Web\Json::encode($arColumns)?>,
		rows: <?=\Bitrix\Main\Web\Json::encode($arResult['ROWS'])?>,
		prices: <?=\Bitrix\Main\Web\Json::encode($arResult['PRICES'])?>,
	});

	new SectionsExtra(
		store, 
		{
			sectionsNode: document.getElementById('<?=$sBlockId?>'),
			pricesNode:  document.getElementById('<?=$sBlockId?>_prices'),
			changelistNode:  document.getElementById('<?=$sBlockId?>_changelist'),

			signedParameters: '<?=$this->getComponent()->getSignedParameters()?>'
		}
	);

}());
</script>