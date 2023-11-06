<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage("RS_B2BPORTAL_COMP_CATALOG_SECTION_TITLE"),
	"DESCRIPTION" => Loc::getMessage("RS_B2BPORTAL_COMP_CATALOG_SECTION_DESCR"),
	"ICON" => "",
	"PATH" => [
		'ID' => 'redsign',
		'NAME' => Loc::getMessage('RS_COMP_TITLE'),
		'CHILD' => [
			"ID" => 'rs_b2bportal',
			"NAME" => Loc::getMessage("RS_B2BPORTAL_COMP_TITLE"),
		]
	],
);