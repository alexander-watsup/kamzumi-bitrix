<?php

use \Redsign\B2BPortal\Services\Sale\PersonalProfile;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\CBitrixComponent::includeComponentClass('bitrix:sale.personal.profile.detail');

class RSB2BPortalPersonalProfileDetail extends PersonalProfileDetail
{

	protected function updateProfileProperties($request, $userOrderProperties)
	{
		$fieldValues = $this->prepareUpdatingProperties($request, $userOrderProperties);

		if ($this->errorCollection->isEmpty())
		{
			$this->executeUpdatingProperties($request, $fieldValues);
		}

		if ($this->errorCollection->isEmpty())
		{
			PersonalProfile::resetUserProfileList();

			if (strlen($request->get('save')) > 0)
			{
				LocalRedirect($this->arParams['PATH_TO_LIST']);
			}
			elseif (strlen($request->get('apply')) > 0)
			{
				LocalRedirect(CComponentEngine::makePathFromTemplate($this->arParams['PATH_TO_DETAIL'], array('ID' => $this->idProfile)));
			}
		}
	}

}
