<?php

defined('B_PROLOG_INCLUDED') || die();

use \Bitrix\Main\IO\File;
use \Bitrix\Main\Application;

\CBitrixComponent::includeComponentClass('bitrix:catalog.section');

$documentRoot = Application::getDocumentRoot();
$parentComponentPath = BX_ROOT.'/components/bitrix/catalog.section/';

$parametersPath = $documentRoot.$parentComponentPath.'.parameters.php';
$file = new File($parametersPath);
if ($file->isExists())
{
	include($file->getPath());
}
