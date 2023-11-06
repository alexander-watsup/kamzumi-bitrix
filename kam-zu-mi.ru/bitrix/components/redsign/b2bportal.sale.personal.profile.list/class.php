<?php

use \Bitrix\Main;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Web\Uri;
use \Bitrix\Main\Localization\Loc;
use \Redsign\B2BPortal\Traits;

use \Bitrix\Sale\Internals\UserPropsTable;
use \Bitrix\Sale\Internals\OrderPropsTable;
use \Bitrix\Sale\Internals\UserPropsValueTable;
use \Redsign\B2BPortal\Services\Sale\PersonalProfile;

defined('B_PROLOG_INCLUDED') || die();

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('sale'))
	return;

if (!Loader::includeModule('redsign.b2bportal'))
	return;

\CBitrixComponent::includeComponentClass('bitrix:sale.personal.profile.list');

class RSB2BPortalPersonalProfileList extends PersonalProfileList
{
	const ACTION_CHANGE_SORT = 'sorting';
	const ACTION_CHANGE_PERPAGE = 'perpage';
	const DEFAULT_PER_PAGE = 20;
	const PROPS_PREFIX = 'PROPS_';
	const NAV_NAME = 'companies';

	use Traits\Sorting;
	use Traits\Paging;

	public function onPrepareComponentParams($params)
	{
		$this->errorCollection = new Main\ErrorCollection();

		$params['AJAX_URL'] = $params['SET_URL'] = $this->getRedirectUrl();

		$params["PATH_TO_DETAIL"] = trim($params["PATH_TO_DETAIL"]);

		if (strlen($params["PATH_TO_DETAIL"]) <= 0)
		{
			$params["PATH_TO_DETAIL"] = htmlspecialcharsbx(Main\Context::getCurrent()->getRequest()->getRequestedPage()."?ID=#ID#");
		}

		$params['PER_PAGE'] = ((int)($params['PER_PAGE']) <= 0 ? self::DEFAULT_PER_PAGE : (int)($params['PER_PAGE']));
		$params['PAGE_ELEMENT_COUNT'] = $params['PER_PAGE'];

		$this->prepareSortingParams($params, self::ACTION_CHANGE_SORT);
		$this->preparePagingParams($params, self::ACTION_CHANGE_PERPAGE);

		if ($params['SORT_BY1'] == 'sale_type')
		{
			$params['ELEMENT_SORT_FIELD'] = $params['SORT_BY1'] = 'PERSON_TYPE_ID';
		}

		$params['CATALOG_SORTER'] = [
			'ACTION_CHANGE_SORT' => self::ACTION_CHANGE_SORT,
			'ACTION_CHANGE_PERPAGE' => self::ACTION_CHANGE_PERPAGE,
			'PERPAGE_DROPDOWN' => $params['PERPAGE_FIELDS'],
		];

		$params['PER_PAGE'] = $params['PAGE_ELEMENT_COUNT'];

		$params['PROPS_CODE'] = !empty($params['PROPS_CODE']) ? $params['PROPS_CODE'] : [];

		$this->deleteElementId = (int) ($this->request->get('del_id'));
		$this->copyElementId = (int) ($this->request->get('copy_id'));

		return $params;
	}

	public function getRedirectUrl()
	{
		$paramsToDelete = ['set_filter', 'del_filter', 'ajax', 'bxajaxid', 'AJAX_CALL', 'mode'];
		$uriString = $this->request->getRequestUri();
		$uri = new Uri($uriString);
		$uri->deleteParams($paramsToDelete);
		$uri->deleteParams(\Bitrix\Main\HttpRequest::getSystemParameters());
		$redirectUrl = $uri->getUri();

		return $redirectUrl;
	}

	public function executeComponent()
	{
		global $APPLICATION;

		$this->setFrameMode(false);

		$this->checkRequiredModules();

		$this->arResult['ERRORS'] = [];

		if ($this->arParams['SET_TITLE'] == 'Y')
			$APPLICATION->SetTitle(GetMessage('SPPL_DEFAULT_TITLE'));

		$this->checkAuth();

		$this->doRequest();

		$this->fillResult();

		$this->includeComponentTemplate();
	}

	public function checkAuth()
	{
		global $APPLICATION, $USER;

		if (!$USER->IsAuthorized())
		{
			if(!$this->arParams['AUTH_FORM_IN_TEMPLATE'])
			{
				$APPLICATION->AuthForm(GetMessage('SALE_ACCESS_DENIED'), false, false, 'N', false);
			}
			else
			{
				$this->arResult['ERRORS'][self::E_NOT_AUTHORIZED] = GetMessage('SALE_ACCESS_DENIED');
			}
		}
	}

	public function fillResult()
	{
		$this->fillProps();
		$this->fillHeaders();

		$this->fillProfiles();
		$this->fillProfilesPropValues();

		$this->fillNavigation();
		$this->fillUrl();
	}

	public function fillProfiles()
	{
		global $APPLICATION, $USER;

		$by = (strlen($this->arParams['ELEMENT_SORT_FIELD']) > 0 ? $this->arParams['ELEMENT_SORT_FIELD'] : 'DATE_UPDATE');
		$order = (strlen($this->arParams['ELEMENT_SORT_ORDER']) > 0 ? $this->arParams['ELEMENT_SORT_ORDER'] : 'DESC');

		$arProfiles = [];

		$sortByProp = false;
		$sortPropCode = false;
		$sortPropCodeFree = str_replace(self::PROPS_PREFIX, '', $by);
		if (in_array($sortPropCodeFree, $this->arParams['PROPS_CODE']))
		{
			$sortPropCode = $by;
			$by = $by.'.VALUE';
			$sortByProp = true;
		}
		else
		{
			$sortPropCodeFree = false;
		}

		$arOrder = [];
		$arFilter = [
			'=USER_ID' => (int) $USER->GetID(),
		];
		$arSelect = [
			'ID',
			'NAME',
			'USER_ID',
			'PERSON_TYPE_ID',
			'DATE_UPDATE',
		];

		$count = UserPropsTable::getCount($arFilter);

		$nav = new \Bitrix\Main\UI\PageNavigation(self::NAV_NAME);
		$nav->allowAllRecords(false)
			->setPageSize($this->arParams['PER_PAGE'])
			->initFromUri();

		$query = UserPropsTable::query();

		if ($sortByProp)
		{
			$ids = [];

			if (!empty($this->arResult['PROPS']))
			{
				foreach ($this->arResult['PROPS'] as $arProp)
				{
					if ($sortPropCodeFree != $arProp['CODE'])
						continue;
					
					$ids[] = $arProp['ID'];
				}
			}

			if (!empty($ids))
			{
				$arSelect[] = $sortPropCode;

				$query->registerRuntimeField(
					(new \Bitrix\Main\ORM\Fields\Relations\Reference(
						$sortPropCode,
						UserPropsValueTable::class,
						\Bitrix\Main\ORM\Query\Join::on('this.ID', 'ref.USER_PROPS_ID')
							->whereIn('ref.ORDER_PROPS_ID', $ids)
					))
				);

				$arOrder[$by] = $order;
			}
		}

		$arOrder['ID'] = 'DESC';

		$query->setSelect($arSelect)
			->setOrder($arOrder)
			->setFilter($arFilter);

		$query->setOffset($nav->getOffset())
			->setLimit($nav->getLimit());

		$rows = $query->exec();
		$nav->setRecordCount($count);

		while ($row = $rows->fetch())
		{
			$arProfile = [
				'ID' => $row['ID'],
				'NAME' => $row['NAME'],
				'USER_ID' => $row['USER_ID'],
				'PERSON_TYPE_ID' => $row['PERSON_TYPE_ID'],
				'DATE_UPDATE' => $row['DATE_UPDATE']->toString(),
			];

			$personTypeList = Bitrix\Sale\PersonType::load(SITE_ID, $arProfile['PERSON_TYPE_ID']);
			$arProfile['PERSON_TYPE'] = $personTypeList[$arProfile['PERSON_TYPE_ID']];
			$arProfile['PERSON_TYPE']['NAME'] = htmlspecialcharsEx($arProfile['PERSON_TYPE']['NAME']);

			$arProfile['PROPS'] = [];

			$arProfile['URL_TO_DETAIL'] = CComponentEngine::MakePathFromTemplate($this->arParams['PATH_TO_DETAIL'], array('ID' => $arProfile['ID']));
			if (empty($this->arParams['PATH_TO_DELETE']))
			{
				$arProfile['URL_TO_DETELE'] = htmlspecialcharsbx($APPLICATION->GetCurPage().'?del_id='.$arProfile["ID"].'&'.bitrix_sessid_get());
			}
			else
			{
				$arProfile['URL_TO_DETELE'] = CComponentEngine::MakePathFromTemplate($this->arParams['PATH_TO_DELETE'], array('ID' => $arProfile['ID']))."&".bitrix_sessid_get();
			}
			if (empty($this->arParams['PATH_TO_COPY']))
			{
				$arProfile['URL_TO_COPY'] = htmlspecialcharsbx($APPLICATION->GetCurPage().'?copy_id='.$arProfile["ID"].'&'.bitrix_sessid_get());
			}
			else
			{
				$arProfile['URL_TO_COPY'] = CComponentEngine::MakePathFromTemplate($this->arParams['PATH_TO_COPY'], array('ID' => $arProfile['ID']))."&".bitrix_sessid_get();
			}

			$arProfiles[$arProfile['ID']] = $arProfile;
		}

		$this->arResult['PROFILES'] = $arProfiles;

		$this->nav = $nav;
	}

	public function fillProfilesPropValues()
	{
		if (empty($this->arResult['PROPS']) || empty($this->arResult['PROFILES']))
			return;

		$propIds = [];
		$propId2Code = [];
		foreach ($this->arResult['PROPS'] as $arProp)
		{
			$propId2Code[$arProp['ID']] = $arProp['CODE'];

			if (!in_array($arProp['CODE'], $this->arParams['PROPS_CODE']))
				continue;

			$propIds[] = $arProp['ID'];
		}

		$profileIds = [];
		foreach ($this->arResult['PROFILES'] as $arProfile)
		{
			$profileIds[] = $arProfile['ID'];
		}

		$arOrder = [];
		$arFilter = [
			'=USER_PROPS_ID' => $profileIds,
			'=ORDER_PROPS_ID' => $propIds,
			'!=VALUE' => false,
		];
		$arSelect = [
			'USER_PROPS_ID',
			'ORDER_PROPS_ID',
			'VALUE',
		];
		$query = UserPropsValueTable::query();
		$query->setOrder($arOrder);
		$query->setFilter($arFilter);
		$query->setSelect($arSelect);
		$res = $query->exec();
		while ($row = $res->fetch())
		{
			$profileId = $row['USER_PROPS_ID'];
			$propCode = self::PROPS_PREFIX.$propId2Code[$row['ORDER_PROPS_ID']];
			$this->arResult['PROFILES'][$profileId]['PROPS'][$propCode] = $row['VALUE'];
		}
	}

	public function fillProps()
	{
		$arProps = [];
		$arOrder = [
			'SORT' => 'ASC',
			'NAME' => 'ASC'
		];
		$arFilter = [
			'=CODE' => $this->arParams['PROPS_CODE'],
		];
		$arSelect = [
			'*'
		];
		$query = OrderPropsTable::query();
		$query->setOrder($arOrder);
		$query->setFilter($arFilter);
		$query->setSelect($arSelect);
		$res = $query->exec();
		while ($row = $res->fetch())
		{
			$arProps[$row['ID']] = [
				'ID' => $row['ID'],
				'CODE' => $row['CODE'],
				'NAME' => $row['NAME'],
				'TYPE' => $row['TYPE'],
			];
		}

		$this->arResult['PROPS'] = $arProps;
	}

	public function fillHeaders()
	{
		$this->arResult['HEADERS'] = [];

		if (empty($this->arResult['PROPS']))
			return;
		
		$arAlreadyWas = [];
		$arHeaders = [];
		foreach ($this->arResult['PROPS'] as $row)
		{
			if (in_array($row['CODE'], $arAlreadyWas))
				continue;

			$arHeaders[$row['ID']] = [
				'ID' => $row['ID'],
				'CODE' => self::PROPS_PREFIX.$row['CODE'],
				'NAME' => $row['NAME'],
				'TYPE' => $row['TYPE'],
			];

			$arAlreadyWas[] = $row['CODE'];
		}

		$this->arResult['HEADERS'] = $arHeaders;
	}

	public function fillNavigation()
	{
		$result = [
			'perPage' => $this->nav->getPageSize(),
			'navName' => $this->nav->getId(),
			'currentPage' => $this->nav->getCurrentPage(),
			'totalRecords' => $this->nav->getRecordCount(),
		];

        $this->arResult['NAV_RESULT'] = $result;
	}

	public function fillUrl()
	{
		if ($this->request->get('SECTION'))
		{
			$this->arResult['URL'] = htmlspecialcharsbx($this->request->getRequestedPage().'?SECTION='.$this->request->get('SECTION').'&');
		}
		else
		{
			$this->arResult['URL'] = htmlspecialcharsbx($this->request->getRequestedPage().'?');
		}
	}

	public function doRequest()
	{
		$this->doRequestDelete();
		$this->doRequestCopy();
	}

	public function doRequestDelete()
	{
		global $APPLICATION;

		$errorMessage = '';
		if ($this->deleteElementId > 0 && check_bitrix_sessid())
		{
			if ($arUserProps = $this->checkIssetProfileById($this->deleteElementId))
			{
				if (!CSaleOrderUserProps::Delete($arUserProps['ID']))
				{
					$errorMessage = GetMessage('SALE_DEL_PROFILE');
				}
			}
			else
			{
				$errorMessage = GetMessage('SALE_NO_PROFILE');
			}

			if (strlen($errorMessage) > 0)
			{
				LocalRedirect($APPLICATION->GetCurPageParam('del_id='.$this->deleteElementId, array('del_id', 'sessid')));
			}
			else
			{
				PersonalProfile::resetUserProfileList();
				LocalRedirect($APPLICATION->GetCurPageParam('success_del_id='.$this->deleteElementId, array('del_id', 'sessid')));
			}
		}

		if ((int)($this->request->get['del_id']) > 0)
			$errorMessage = GetMessage('SALE_DEL_PROFILE', array('#ID#' => (int)($this->request->get['del_id'])));
		elseif ((int)($this->request->get['success_del_id']) > 0)
			$errorMessage = GetMessage('SALE_DEL_PROFILE_SUC', array('#ID#' => (int)($this->request->get['success_del_id'])));

		if (strlen($errorMessage) > 0)
		{
			$this->arResult['ERROR_MESSAGE'] = $errorMessage;
			$this->arResult['ERRORS'][] = $errorMessage;
		}
	}

	public function doRequestCopy()
	{
		global $APPLICATION;

		if ($this->copyElementId > 0 && check_bitrix_sessid())
		{
			$errorMessage = '';
			if ($arUserProps = $this->checkIssetProfileById($this->copyElementId))
			{
				$newProfileId = $this->copyProfile($arUserProps['ID']);
				$this->copyProfileProps($newProfileId, $arUserProps['ID']);
			}
			else
			{
				$errorMessage = GetMessage('SALE_NO_PROFILE');
			}

			if (strlen($errorMessage) > 0)
			{
				LocalRedirect($APPLICATION->GetCurPageParam('copy_id='.$this->copyElementId, array('copy_id', 'sessid')));
			}
			else
			{
				PersonalProfile::resetUserProfileList();
				LocalRedirect($APPLICATION->GetCurPageParam('success_copy_id='.$this->copyElementId, array('copy_id', 'sessid')));
			}
		}
	}

	public function copyProfile($copyProfileId)
	{
		global $USER;

		$saleProps = new \CSaleOrderUserProps;
		$dbUserProps = $saleProps::GetList(
			[],
			['ID' => $copyProfileId]
		);
		if ($arUserProps = $dbUserProps->GetNext())
		{
			$arFields = [
				'NAME' => trim(htmlspecialchars_decode($arUserProps['NAME'])),
				'USER_ID' => trim($USER->getId()),
				'PERSON_TYPE_ID' => $arUserProps['PERSON_TYPE_ID'],
			];

			$newProfileId = $saleProps->Add($arFields);
			if ($newProfileId)
			{
				return $newProfileId;
			}

			return false;
		}

		return false;
	}

	public function copyProfileProps($newProfileId, $oldProfileId)
	{
		$arOrder = [];
		$arFilter = [
			'USER_PROPS_ID' => $oldProfileId,
		];
		$arSelect = [
			'ID',
			'USER_PROPS_ID',
			'ORDER_PROPS_ID',
			'NAME',
			'VALUE',
			'PROPERTY_TYPE' => 'PROPERTY.TYPE',
		];
		$query = \Bitrix\Sale\Internals\UserPropsValueTable::query();
		$query->setOrder($arOrder);
		$query->setFilter($arFilter);
		$query->setSelect($arSelect);
		$res = $query->exec();
		while ($row = $res->fetch())
		{
			if ($row['PROPERTY_TYPE'] == 'FILE')
			{
				$value = unserialize(htmlspecialchars_decode($row['VALUE']));
				if (!empty($value))
				{
					$arNewFiles = [];
					foreach ($value as $fileId)
					{
						$arNewFiles[] = \CFile::CopyFile($fileId);
					}

					$value = $arNewFiles;
				}

				$value = serialize($value);
			}
			else
			{
				$value = $row['VALUE'];
			}

			$arFields = [
				'USER_PROPS_ID' => $newProfileId,
				'ORDER_PROPS_ID' => $row['ORDER_PROPS_ID'],
				'NAME' => $row['NAME'],
				'VALUE' => $value,
			];

			\Bitrix\Sale\Internals\UserPropsValueTable::add($arFields);
		}
	}

	public function checkIssetProfileById($id)
	{
		global $USER;

		$dbUserProps = CSaleOrderUserProps::GetList(
			[],
			[
				'ID' => $id,
				'USER_ID' => (int) ($USER->GetID())
			]
		);
		if ($arUserProps = $dbUserProps->Fetch())
		{
			return $arUserProps;
		}

		return false;
	}

}
