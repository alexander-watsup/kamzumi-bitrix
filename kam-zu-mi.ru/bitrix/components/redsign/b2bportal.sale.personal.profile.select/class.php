<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\SystemException;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Engine\ActionFilter;
use \Bitrix\Main\Engine\Contract\Controllerable;

use \Bitrix\Sale\PersonType;
use \Redsign\B2BPortal\Services\Sale\PersonalProfile;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('redsign.b2bportal'))
{
	ShowError(Loc::getMessage('RS_B2BPORTAL_MODULE_B2BPORTAL_NOT_INSTALLED'));
}

class RedsignB2BPortalPersonalProfileSelect extends CBitrixComponent implements Controllerable
{

	protected $profileSelectId = 0;
	protected $profiles = [];
	protected $personTypes = [];

	/**
	 * standart bitrix function for ajax treatment
	 *
	 * @return array
	 */
	public function configureActions(): array
	{
		return [
			'setCurrentProfile' => [
				'prefilters' => [
					new ActionFilter\Csrf(),
				],
			],
		];
	}

	public function onPrepareComponentParams(array $params): array
	{
		$this->profileSelectId = PersonalProfile::getSelectedProfile();

		return $params;
	}

	protected function fillPersonTypes()
	{
		$this->arResult['PERSON_TYPES'] = $this->personTypes = PersonType::load($this->getSiteId());
	}

	protected function fillProfiles(): void
	{
		global $USER;
		if (!$USER->IsAuthorized())
			return;

		$this->profiles = PersonalProfile::getUserProfileList();

		$bChecked = false;
		$arProfiles = [];
		foreach ($this->profiles as $row)
		{
			$row['CHECKED'] = 'N';
			if (!empty($this->profileSelectId) && $this->profileSelectId == $row['ID'])
			{
				$row['CHECKED'] = 'Y';
				$bChecked = true;
			}
			elseif (!$bChecked && empty($this->profileSelectId))
			{
				$row['CHECKED'] = 'Y';
				$bChecked = true;
			}

			$arProfiles[$row['ID']] = $row;
		}

		$this->arResult['PROFILES'] = $this->profiles = $arProfiles;
	}

	protected function fillUser()
	{
		global $USER;

		if ($USER && $USER instanceof CUser && $USER->IsAuthorized())
		{
			$this->arResult['USER'] = [];
			$this->arResult['USER']['LOGIN'] = $USER->GetLogin();
			$this->arResult['USER']['FULLNAME'] = $USER->GetFullName();

			$this->arResult['USER']['INITIALS'] = '';
			if (strlen($USER->GetFirstName()) > 0)
			{
				$this->arResult['USER']['INITIALS'] .= substr($USER->GetFirstName(), 0, 1);

				if (strlen($USER->GetLastName()) > 0)
				{
					$this->arResult['USER']['INITIALS'] .= substr($USER->GetLastName(), 0, 1);
				}
			}
			else
			{
				$this->arResult['USER']['LOGIN'] .= substr($USER->GetLastName(), 0, 2);
			}
		}
	}

	public function executeComponent(): void
	{
		global $USER;

		try {

			$this->setFrameMode(false);

			$this->checkModules();
	
			if ($USER->IsAuthorized())
			{
				$this->arResult['FORM_TYPE'] = 'logout';

				$this->fillPersonTypes();
				$this->fillProfiles();
				$this->fillUser();
			}
			else
			{
				$this->arResult['FORM_TYPE'] = 'login';
			}

			$this->includeComponentTemplate();
		}
		catch(SystemException $e)
		{
			\ShowError($e->getMessage());
		}
	}

	public function setCurrentProfileAction($id): array
	{
		$bProfileSetted = PersonalProfile::setSelectedProfile($id);

		if ($bProfileSetted === false)
		{
			return ['result' => false, 'ERRORS' => ['Unknown profile with id = '.$id]];
		}
		else
		{
			return ['result' => true];
		}
	}

	protected function checkModules(): void
	{
		if (!Loader::includeModule('sale'))
		{
			throw new SystemException(
				Loc::getMessage('RS_B2BPORTAL_MODULE_SALE_NOT_INSTALLED')
			);
		}
	}

}
