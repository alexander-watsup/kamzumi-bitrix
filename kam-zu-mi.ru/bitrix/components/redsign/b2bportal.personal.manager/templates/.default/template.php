<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) 
{
	die();
}

if ($arResult['HAS_MANAGER'] && !empty($arResult['MANAGER_DATA']))
{
	echo $arResult['MANAGER_DATA']['NAME'];
}