<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
	die();
}

use \Bitrix\Main\Localization\Loc;

$arComponentParameters = [
	'PARAMETERS' => [
		'NEED_CONFIRM' => [
			'NAME' => Loc::getMessage('RS_VSA_PARAMETERS_NEED_CONFIRM'),
			'TYPE' => 'CHECKBOX',
			'MULTIPLE' => 'N',
			'DEFAULT' => 'N',
			'PARENT' => 'BASE',
		],
		'PATH_TO_CART' => [
			'NAME' => Loc::getMessage('RS_VSA_PARAMETERS_PATH_TO_CART'),
			'TYPE' => 'STRING',
			'MUTLIPLE' => 'N',
			'DEFAULT' => '/personal/cart/',
			'PARENT' => 'URL_SETTINGS'
		]
	]
];