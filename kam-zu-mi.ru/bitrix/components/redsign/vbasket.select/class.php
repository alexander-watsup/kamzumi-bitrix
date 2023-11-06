<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\SystemException;

use Redsign\VBasket\Core;
use Redsign\VBasket\BasketHelper;

class RedsignVBasketSelect extends CBitrixComponent
{
	protected $active;
	protected $basketCollection;
	protected $counts;

	public function __construct($component = null)
	{
		parent::__construct($component);
	}

	public function onPrepareComponentParams($params)
	{
		$params['USE_COUNTS'] = !isset($params['USE_COUNTS']) || !in_array($params['USE_COUNTS'], ['Y', 'N']);
		
		return $params;
	}

	protected function checkModules()
	{
		if (!Loader::includeModule('redsign.vbasket'))
		{
			throw new SystemException('redsign.vbasket module not installed');
		}
	}

	protected function getResult()
	{
		$this->arResult = BasketHelper::getAllBasketsByCurrentContext($this->arParams['USE_COUNTS']);
	}

	public function executeComponent(): void
	{
		try
		{
			if (Core::isEnabled())
			{				
				$this->checkModules();
				$this->setFrameMode(false);

				$this->getResult();

				$this->includeComponentTemplate();
			}
		}
		catch(SystemException $e)
		{
			\ShowError($e->getMessage());
		}
	}
}