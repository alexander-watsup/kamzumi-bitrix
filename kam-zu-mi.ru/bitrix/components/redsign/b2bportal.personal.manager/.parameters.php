<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) 
{
	die();
}

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Iblock\IblockTable;

if(!\Bitrix\Main\Loader::includeModule('iblock'))
{
	return;
}

$iblockTypes = CIBlockParameters::GetIBlockTypes(['-' => ' ']);

$iblocks = [];
$iblocksIterator = CIBlock::GetList(
	['SORT' => 'ASC'],
	[
		'SITE_ID' => $_REQUEST['site'], 
		'TYPE' => ($arCurrentValues['IBLOCK_TYPE'] !== '-' ? $arCurrentValues['IBLOCK_TYPE'] : '')
	]
);

while ($iblockData = $iblocksIterator->fetch())
{
	$iblocks[$iblockData['ID']] = '['.$iblockData['ID'].'] '.$iblockData['NAME'];
}

$props = array();
$propsIterator = CIBlockProperty::GetList(
	['sort' => 'asc', 'name' => 'asc'], 
	[
		'ACTIVE' => 'Y', 
		'IBLOCK_ID' => (isset($arCurrentValues['IBLOCK_ID']) ? $arCurrentValues['IBLOCK_ID'] : $arCurrentValues['ID'])
	]
);
while ($arr = $propsIterator->Fetch())
{
	$arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	if (in_array($arr['PROPERTY_TYPE'], array('L', 'N', 'S', 'F')))
	{
		$props[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
	}
}


$arComponentParameters = [
	'GROUPS' => [],
	'PARAMETERS' => [
		'IBLOCK_TYPE' => [
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('T_IBLOCK_DESC_LIST_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => $iblockTypes,
			'DEFAULT' => '-',
			'REFRESH' => 'Y' 			
		],
		'IBLOCK_ID' => [
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage("T_IBLOCK_DESC_LIST_ID"),
			'TYPE' => 'LIST',
			'VALUES' => $iblocks,
			'DEFAULT' => '',
			'REFRESH' => 'Y',
		],
		'PROPS' => [
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('T_IBLOCK_PROPERTY'),
			'TYPE' => 'LIST',
			'MULTIPLE' => 'Y',
			'VALUES' => $props,
			'DEFAULT' => array_keys($props),
			'ADDITIONAL_VALUES' => 'Y',
		],
		'CACHE_TIME'  => ['DEFAULT' => 36000000],
	]
];