<?php

use Bitrix\ 
{
	Main\Context,
	Main\Loader,
	Main\SystemException,
	Main\Localization\Loc
};

use Redsign\B2BPortal\Services\PersonalManager;


Loc::loadMessages(__FILE__);

class RedsignB2BPortalPersonalManager extends CBitrixComponent
{

	protected $user = null;
	protected $data = [];

	public function __construct($component = null)
	{
		parent::__construct($component);

		global $USER;
		$this->user = $USER;
	}

	protected function checkModules(): void
	{
		
		if (!Loader::includeModule('iblock'))
		{
			throw new SystemException(
				Loc::getMessage('RS_B2BPORTAL_BS_MODULE_IBLOCK_NOT_INSTALLED')
			);
		}
	}

	public function onPrepareComponentParams(array $params): array
	{
		if (empty($params['PROPS']))
		{
			$params['PROPS'] = ['NAME'];
		}

		if (empty($params['SHOW_DEFAULT_FOR_UNAUTHORIZED']))
		{
			$params['SHOW_DEFAULT_FOR_UNAUTHORIZED'] = 'N';
		}

		return $params;
	}

	protected function getResult(): void
	{
		if ($this->user->IsAuthorized())
		{
			$this->arResult['DATA'] = PersonalManager::getForUser(
				(int) $this->user->GetId(), 
				(int) $this->arParams['IBLOCK_ID'], 
				$this->arParams['PROPS'], 
				true
			);

		}
		elseif ($this->arParams['SHOW_DEFAULT_FOR_UNAUTHORIZED'] == 'Y')
		{
			$this->arResult['DATA'] = PersonalManager::getDefault(
				$this->arParams['IBLOCK_ID'],
				$this->arParams['PROPS']
			);
		}

		$this->arResult['HAS_MANAGER'] = !empty($this->arResult['DATA']);

		global $CACHE_MANAGER;
		$CACHE_MANAGER->RegisterTag("IBLOCK_ID_".$this->arParams['IBLOCK_ID']);   
	}

	protected function getCacheParams()
	{
		return serialize(
				array_merge(
					$this->arParams,
					$this->user->IsAuthorized() ? ['MANAGER_ID' => PersonalManager::getValueForUser($this->user->GetId())] : []
				)
		);
	}

	public function executeComponent(): void
	{
		try 
		{
			$this->setFrameMode(false);
			$this->checkModules();

			if ($this->startResultCache(false, $this->getCacheParams()))
			{
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