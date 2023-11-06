<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}

use \Bitrix\Main\Loader;
use \Redsign\VBasket\Core;


if (Loader::includeModule('redsign.vbasket'))
{
	$arResult['USE_VBASKET'] = Core::isEnabled();
	
	if ($arResult['USE_VBASKET'])
	{		
		$arResult['VBASKET_SELECTED'] = false;
		$arResult['VBASKET_NOT_SELECTED'] = false;

		
		$context = Core::container()->get('context');
		$basketRepository = Core::container()->get('basket_repository');
		$basketItemsRepository = Core::container()->get('basket_item_repository');
		
		$basketCollection = $basketRepository->getAllByContext($context);
		
		$counts = $basketItemsRepository->getCounts($basketCollection);
		
		foreach ($basketCollection as $basketEntityObject)
		{
			$basket = [
				'ID' => $basketEntityObject->getId(),
				'CODE' => $basketEntityObject->getCode(),
				'~NAME' => $basketEntityObject->getName(),
				'NAME' => htmlspecialcharsbx($basketEntityObject->getName()),
				'~COLOR' => $basketEntityObject->getColor(),
				'COLOR' => htmlspecialcharsbx($basketEntityObject->getColor()),
				'SELECTED' => \Redsign\VBasket\BasketHelper::isCurrentBasket($basketEntityObject->getCode()),
				'CNT' => isset($counts[$basketEntityObject->getId()]) ? (int) $counts[$basketEntityObject->getId()] : 0
			];

			if ($basket['SELECTED'])
			{
				$arResult['VBASKET_SELECTED'] = $basket;
			}
			else
			{
				$arResult['VBASKET_NOT_SELECTED'][] = $basket;
			}
		}
	}
}
else
{
	$arResult['USE_VBASKET'] = false;
}