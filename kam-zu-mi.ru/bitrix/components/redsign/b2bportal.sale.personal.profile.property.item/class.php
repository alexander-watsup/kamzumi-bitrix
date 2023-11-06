<?php

defined('B_PROLOG_INCLUDED') || die();

class RSB2BPortalPersonalProfilePropertyItem extends CBitrixComponent
{

	public function onPrepareComponentParams($params)
	{
		if (!empty($params['RESULT']))
		{
			$this->arResult = $params['RESULT'];
			unset($params['RESULT']);
		}

		if (!empty($params['PARAMS']))
		{
			$params += $params['PARAMS'];
			unset($params['PARAMS']);
		}
		
		return $params;
	}

	public function executeComponent()
	{
		$this->includeComponentTemplate();
	}

}
