<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;

$this->addExternalJs(SITE_TEMPLATE_PATH.'/assets/vendors/vue/vue.js');
$this->addExternalJS($templateFolder.'/js/component.js');

$sBlockId = 'searchArticle_'.$this->randString(5);

?>
<div id="<?=$sBlockId?>"></div>
<script>
BX.message({
	'RS_B2BPORTAL_CSP_ADDTOBASKET_SUCCESS': '<?=Loc::getMessage('RS_B2BPORTAL_CSP_ADDTOBASKET_SUCCESS')?>',
	'RS_B2BPORTAL_CSP_INPUT_PLACEHOLDER': '<?=Loc::getMessage('RS_B2BPORTAL_CSP_INPUT_PLACEHOLDER')?>'
});

new CatalogSearchArticle(
	document.getElementById('<?=$sBlockId?>'),
	{
		signedParameters: '<?=$this->getComponent()->getSignedParameters()?>'
	}
);
</script>
