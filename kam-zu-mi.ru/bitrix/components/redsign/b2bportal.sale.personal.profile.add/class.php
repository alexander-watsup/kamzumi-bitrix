<?php

use \Bitrix\Main;
use \Bitrix\Sale;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

use \Bitrix\Sale\PersonType;
use \Bitrix\Sale\Internals\OrderPropsTable;
use \Bitrix\Sale\Internals\OrderPropsGroupTable;

use \Redsign\B2BPortal\Services\Sale\PersonalProfile;

defined('B_PROLOG_INCLUDED') || die();

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('redsign.b2bportal'))
	return;

if (!Loader::includeModule('sale'))
	return;

class RSB2BPortalPersonalProfileAdd extends CBitrixComponent
{
	const E_SALE_MODULE_NOT_INSTALLED = 10000;
	const E_NOT_AUTHORIZED = 10001;

	/** @var  Main\ErrorCollection $errorCollection*/
	protected $errorCollection;

	protected $idProfile;

	public $personTypeId;
	public $personTypes;
	public $group;
	public $property;

	/**
	 * Function checks and prepares all the parameters passed. Everything about $arParam modification is here.
	 * @param $params		Parameters of component.
	 * @return array		Checked and valid parameters.
	 */
	public function onPrepareComponentParams($params)
	{
		global $APPLICATION;

		$this->errorCollection = new Main\ErrorCollection();

		$this->idProfile = 0;

		if (isset($params['PATH_TO_LIST']))
		{
			$params['PATH_TO_LIST'] = trim($params['PATH_TO_LIST']);
		}

		if ($params["PATH_TO_DETAIL"] !== '')
		{
			$params["PATH_TO_DETAIL"] = trim($params["PATH_TO_DETAIL"]);
		}
		else
		{
			$params["PATH_TO_DETAIL"] = htmlspecialcharsbx($APPLICATION->GetCurPage()."?ID=#ID#");
		}

		if (!isset($params['COMPATIBLE_LOCATION_MODE']) && $this->initComponentTemplate())
		{
			$template = $this->getTemplate();
			if ($template instanceof CBitrixComponentTemplate
				&& $template->GetSiteTemplate() == ''
				&& $template->GetName() == '.default'
			)
				$params['COMPATIBLE_LOCATION_MODE'] = 'N';
			else
				$params['COMPATIBLE_LOCATION_MODE'] = 'Y';
		}
		else
		{
			$params['COMPATIBLE_LOCATION_MODE'] = $params['COMPATIBLE_LOCATION_MODE'] == 'Y' ? 'Y' : 'N';
		}

		if (!empty($this->request->get('personTypeid')))
		{
			$r = PersonType::getList([
				'filter' => [
					'ID' => $this->request->get('personTypeid')
				]
			])->fetchAll();
			$personType = $r ? $r[0] : [];
			if (!empty($personType))
			{
				$this->personTypeId = $personType['ID'];
			}
		}

		return $params;
	}

	public function executeComponent()
	{
		global $USER, $APPLICATION;

		$this->setFrameMode(false);

		$this->checkRequiredModules();

		if (!$USER->IsAuthorized())
		{
			if(!$this->arParams['AUTH_FORM_IN_TEMPLATE'])
			{
				$APPLICATION->AuthForm(Loc::getMessage("SALE_ACCESS_DENIED"), false, false, 'N', false);
			}
			else
			{
				$this->errorCollection->setError(new Main\Error(Loc::getMessage("SALE_ACCESS_DENIED"), self::E_NOT_AUTHORIZED));
			}
		}

		if ($this->arParams['AJAX_CALL'] === 'Y')
		{
			return $this->responseAjax();
		}

		if ($this->arParams["SET_TITLE"] === 'Y')
		{
			$APPLICATION->SetTitle(Loc::getMessage("SPPD_TITLE"));
		}

		if ($this->errorCollection->isEmpty())
		{
			$this->fillPersonTypes();

			$this->fillProps();

			$this->fillPropsValue();

			$this->fillResult();
		}

		if (
			$this->request->isPost()
			&& ($this->request->get('save') || $this->request->get("apply"))
			&& check_bitrix_sessid()
		)
		{
			$this->fillProfile();
		}

		$this->formatResultErrors();

		$this->includeComponentTemplate();
	}

	protected function fillPersonTypes()
	{
		$bFirst = true;
		$personTypes = PersonType::load($this->getSiteId());
		foreach ($personTypes as $key1 => $personType)
		{
			if ($bFirst && empty($this->personTypeId))
			{
				$personType['CHECKED'] = 'Y';
				$bFirst = false;
				$this->personTypeId = $personType['ID'];
			}
			elseif ($this->personTypeId == $personType['ID'])
			{
				$personType['CHECKED'] = 'Y';
				$this->personTypeId = $personType['ID'];
			}

			$personTypes[$key1] = $personType;
		}

		$this->personTypes = $personTypes;
	}

	protected function fillProps()
	{
		if (empty($this->personTypeId))
			return;

		$arGroups = [];
		$arOrder = [
			'SORT' => 'ASC',
			'NAME' => 'ASC'
		];
		$arFilter = [
			'PERSON_TYPE_ID' => $this->personTypeId,
		];
		$arSelect = [
			'*'
		];
		$query = OrderPropsGroupTable::query();
		$query->setOrder($arOrder);
		$query->setFilter($arFilter);
		$query->setSelect($arSelect);
		$res = $query->exec();
		while ($row = $res->fetch())
		{
			$arGroups[$row['ID']] = $row;
		}
		$this->group = $arGroups;

		$arProps = [];
		$arOrder = [
			'SORT' => 'ASC',
			'NAME' => 'ASC'
		];
		$arFilter = [
			'PERSON_TYPE_ID' => $this->personTypeId,
			'USER_PROPS' => 'Y',
			'ACTIVE' => 'Y',
			'UTIL' => 'N',
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
			$arProps[$row['ID']] = $row;

			if (in_array($row['TYPE'], array('SELECT', 'MULTISELECT', 'RADIO', 'ENUM')))
			{
				$dbVars = \CSaleOrderPropsVariant::GetList(($by = "SORT"), ($order = "ASC"), array("ORDER_PROPS_ID" => $row["ID"]));
				while ($vars = $dbVars->GetNext())
				{
					$arProps[$row['ID']]['VALUES'][] = $vars;
				}
			}
		}
		$this->property = $arProps;
	}

	public function fillPropsValue()
	{
		$this->arResult['NAME'] = trim($this->request->get('NAME'));

		$propertiesValueList = [];

		$htmlConvector = \Bitrix\Main\Text\Converter::getHtmlConverter();

		$orderPropertiesList = CSaleOrderProps::GetList(
			array("SORT" => "ASC", "NAME" => "ASC"),
			array(
				"PERSON_TYPE_ID" => $this->personTypeId,
				"USER_PROPS" => "Y", "ACTIVE" => "Y", "UTIL" => "N"
			),
			false,
			false,
			array("ID", "NAME", "TYPE", "REQUIED", "MULTIPLE", "IS_LOCATION", "PROPS_GROUP_ID", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "SORT")
		);

		while ($orderProperty = $orderPropertiesList->GetNext())
		{
			$value = $this->request->get('ORDER_PROP_'.$orderProperty['ID']);

			if (is_array($value))
			{
				foreach ($value as &$elementValue)
				{
					if (!is_array($elementValue))
						$elementValue = $htmlConvector->encode($elementValue);
					else
						$elementValue = htmlspecialcharsEx($elementValue);
				}
			}
			else
			{
				$value = $htmlConvector->encode($value);
			}

			if (
				$orderProperty['TYPE'] == 'MULTISELECT'
				|| ($orderProperty['TYPE'] == 'ENUM' && $orderProperty['MULTIPLE'] == 'Y')
			)
			{
				$value = implode(',', $value);
			}

			$propertiesValueList['ORDER_PROP_'.$orderProperty['ID']] = $value;
		}

		// if (!empty($orderPropertyList))
		// {
		// 	foreach ($orderPropertyList as $propertyId => $propValue)
		// 	{
		// 		if (
		// 			($propValue['MULTIPLE'] === 'Y' || $propValue['TYPE'] === 'FILE')
		// 			&& CheckSerializedData($propValue['VALUE'])
		// 			&& ($serialisedValue = @unserialize($propValue['VALUE'])) !== false
		// 		)
		// 		{
		// 			$value = $propValue['VALUE'] = $serialisedValue;
		// 		}
		// 		else
		// 		{
		// 			$value = $propValue['VALUE'];
		// 		}

		// 		if ($orderPropertyList[$propertyId]['TYPE'] === 'LOCATION')
		// 		{
		// 			$value = ;
		// 		}

		// 		if (is_array($value))
		// 		{
		// 			foreach ($value as &$elementValue)
		// 			{
		// 				if (!is_array($elementValue))
		// 					$elementValue = $htmlConvector->encode($elementValue);
		// 				else
		// 					$elementValue = htmlspecialcharsEx($elementValue);
		// 			}
		// 		}
		// 		else
		// 		{
		// 			$value = $htmlConvector->encode($value);
		// 		}
		// 		$propertiesValueList['ORDER_PROP_'.$propertyId] = $value;
		// 	}
		// }

		$this->orderPropsValue = $propertiesValueList;

	}

	public function fillResult()
	{
		$this->arResult['PERSON_TYPE_ID_CHECKED'] = $this->personTypeId;
		$this->arResult['PERSON_TYPE'] = $this->personTypes;
		$this->arResult['GROUP'] = $this->group;
		$this->arResult['PROPERTY'] = $this->property;
		$this->arResult['ORDER_PROPS_VALUES'] = $this->orderPropsValue;
	}

	protected function fillProfile()
	{
		$fieldValues = $this->prepareProperties();

		if ($this->errorCollection->isEmpty())
		{
			$this->addProfile();

			$this->executeProperties($fieldValues);
		}

		if ($this->errorCollection->isEmpty())
		{
			PersonalProfile::resetUserProfileList();

			if (strlen($this->request->get('save')) > 0)
			{
				LocalRedirect($this->arParams['PATH_TO_LIST']);
			}
			elseif (strlen($this->request->get('apply')) > 0)
			{
				LocalRedirect(CComponentEngine::makePathFromTemplate($this->arParams['PATH_TO_DETAIL'], ['ID' => $this->idProfile]));
			}
		}
	}

	protected function addProfile()
	{
		global $USER;

		if (strlen($this->request->get('NAME')) <= 0)
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage("SALE_NO_NAME")."<br>"));
		}

		if (!$this->errorCollection->isEmpty())
			return;

		$arFields = [
			'NAME' => trim($this->request->get('NAME')),
			'USER_ID' => trim($USER->getId()),
			'PERSON_TYPE_ID' => trim($this->personTypeId),
		];

		$saleProps = new \CSaleOrderUserProps;
		$this->idProfile = $saleProps->Add($arFields);
		if (!$this->idProfile)
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage('SALE_ERROR_ADD_PROF')."<br />"));
			return;
		}
	}

	protected function prepareProperties()
	{
		if (!$this->errorCollection->isEmpty())
			return;

		if (strlen($this->request->get('NAME')) <= 0)
		{
			$this->errorCollection->setError(new Main\Error(Loc::getMessage("SALE_NO_NAME")."<br>"));
		}

		$fieldValues = array();
		$orderPropertiesList = CSaleOrderProps::GetList(
			array("SORT" => "ASC", "NAME" => "ASC"),
			array(
				"PERSON_TYPE_ID" => $this->personTypeId,
				"USER_PROPS" => "Y", "ACTIVE" => "Y", "UTIL" => "N"
			),
			false,
			false,
			array("ID", "NAME", "TYPE", "REQUIED", "MULTIPLE", "IS_LOCATION", "PROPS_GROUP_ID", "IS_EMAIL", "IS_PROFILE_NAME", "IS_PAYER", "IS_LOCATION4TAX", "CODE", "SORT")
		);

		while ($orderProperty = $orderPropertiesList->GetNext())
		{
			$currentValue = $this->request->get('ORDER_PROP_'.$orderProperty['ID']);

			if ($this->checkProperty($orderProperty, $currentValue))
			{
				$fieldValues[$orderProperty["ID"]] = array(
					'USER_PROPS_ID' => $this->idProfile,
					'ORDER_PROPS_ID' => $orderProperty['ID'],
					'NAME' => $orderProperty['NAME'],
					'MULTIPLE' => $orderProperty['MULTIPLE']
				);

				if ($orderProperty['TYPE'] == 'LOCATION')
				{
					$changedLocation = array();
					$locationResult = Sale\Location\LocationTable::getList(
						array(
							'filter' => array('=ID' => $currentValue),
							'select' => array('ID', 'CODE')
						)
					);

					while ($location = $locationResult->fetch())
					{
						if ($orderProperty['MULTIPLE'] === "Y")
						{
							$changedLocation[] = $location['CODE'];
						}
						else
						{
							$changedLocation = $location['CODE'];
						}
					}
					$currentValue = !empty($changedLocation) ? $changedLocation : '';
				}

				if ($orderProperty['TYPE'] === 'FILE')
				{
					$fileIdList = array();

					$currentValue = $this->request->getFile("ORDER_PROP_" . $orderProperty["ID"]);

					foreach ($currentValue['name'] as $key => $fileName)
					{
						if (strlen($fileName) > 0)
						{
							$fileArray = array(
								'name' => $fileName,
								'type' => $currentValue['type'][$key],
								'tmp_name' => $currentValue['tmp_name'][$key],
								'error' => $currentValue['error'][$key],
								'size' => $currentValue['size'][$key],
							);

							$fileIdList[] = CFile::SaveFile($fileArray, "/sale/profile/");
						}
					}

					$fieldValues[$orderProperty["ID"]]['VALUE'] = $fileIdList;
				}
				elseif (
					$orderProperty['TYPE'] == 'MULTISELECT'
					|| ($orderProperty['TYPE'] == 'ENUM' && $orderProperty['MULTIPLE'] == 'Y')
				)
				{
					$fieldValues[$orderProperty["ID"]]['VALUE'] = implode(',', $currentValue);
				}
				elseif ($orderProperty['MULTIPLE'] == "Y")
				{
					if (is_array($currentValue))
					{
						$currentValue = array_diff($currentValue, array("", NULL, false));
					}
					$fieldValues[$orderProperty["ID"]]['VALUE'] = serialize($currentValue);
				}
				else
				{
					$fieldValues[$orderProperty["ID"]]['VALUE'] = $currentValue;
				}
			}
			else
			{
				$this->errorCollection->setError(new Main\Error(Loc::getMessage("SALE_NO_FIELD") . " \"" . $orderProperty["NAME"] . "\".<br />"));
			}
		}

		return $fieldValues;
	}

	protected function executeProperties($fieldValues)
	{
		if (!$this->errorCollection->isEmpty())
			return;

		$saleOrderUserPropertiesValue = new CSaleOrderUserPropsValue;

		foreach ($fieldValues as $arFields)
		{
			$arFields['USER_PROPS_ID'] = $this->idProfile;
			unset($arFields['MULTIPLE']);
			$saleOrderUserPropertiesValue->Add($arFields);
		}
	}

	protected function checkRequiredModules()
	{
		if (!Loader::includeModule('sale'))
		{
			throw new Main\SystemException(Loc::getMessage("SALE_MODULE_NOT_INSTALL"), self::E_SALE_MODULE_NOT_INSTALLED);
		}
	}

	protected function responseAjax()
	{
		if ($this->arParams['ACTION'] === 'getLocationHtml')
		{
			$name = $this->arParams['LOCATION_NAME'];
			$key = $this->arParams['LOCATION_KEY'];
			$locationTemplate = $this->arParams['LOCATION_TEMPLATE'];
			$resultHtml =  $this->getLocationHtml($name, $key, $locationTemplate);
			echo $resultHtml;
		}

		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
		die();
	}

	protected function getLocationHtml($name, $key, $locationTemplate)
	{
		$name = strlen($name) > 0 ? $name : "" ;
		$key = (int)$key >= 0 ? (int)$this->arParams['LOCATION_KEY'] : 0;
		$locationTemplate = strlen($locationTemplate) > 0 ? $locationTemplate : '';
		$locationClassName = 'location-block-wrapper';
		if (empty($locationTemplate))
		{
			$locationClassName .= ' location-block-wrapper-delimeter';
		}
		ob_start();
		CSaleLocation::proxySaleAjaxLocationsComponent(
			array(
				"ID" => "propertyLocation".$name."[$key]",
				"AJAX_CALL" => "N",
				'CITY_OUT_LOCATION' => 'Y',
				'COUNTRY_INPUT_NAME' => $name.'_COUNTRY',
				'CITY_INPUT_NAME' => $name."[$key]",
				'LOCATION_VALUE' => ''
			),
			array(
			),
			$locationTemplate,
			true,
			$locationClassName
		);
		$resultHtml = ob_get_contents();
		ob_end_clean();
		return $resultHtml;
	}

	protected function formatResultErrors()
	{
		if (!$this->errorCollection->isEmpty())
		{
			foreach ($this->errorCollection->toArray() as $error)
			{
				$this->arResult['ERROR_MESSAGE'] .= $error->getMessage();

				if ($error->getCode())
					$this->arResult['ERRORS'][$error->getCode()] = $error->getMessage();
				else
					$this->arResult['ERRORS'][] = $error->getMessage();
			}
		}
	}

	protected function checkProperty($property, $currentValue)
	{
		if ($property["TYPE"] == "LOCATION" && $property["IS_LOCATION"] == "Y")
		{
			if ((int)($currentValue) <= 0)
				return false;
		}
		elseif ($property["IS_PROFILE_NAME"] == "Y")
		{
			if (strlen(trim($currentValue)) <= 0)
				return false;
		}
		elseif ($property["IS_PAYER"] == "Y")
		{
			if (strlen(trim($currentValue)) <= 0)
				return false;
		}
		elseif ($property["IS_EMAIL"] == "Y")
		{
			if (strlen(trim($currentValue)) <= 0 || !check_email(trim($currentValue)))
				return false;
		}
		elseif ($property["REQUIED"] == "Y")
		{
			if ($property["TYPE"] == "LOCATION")
			{
				if ((int)($currentValue) <= 0)
					return false;
			}
			elseif ($property["TYPE"] == "MULTISELECT" || $property["MULTIPLE"] == "Y")
			{
				if (!is_array($currentValue) || count($currentValue) <= 0)
					return false;
			}
			else
			{
				if (strlen($currentValue) <= 0)
					return false;
			}
		}

		return true;
	}

	protected function deleteFromPropertyTypeFile($idFileDeletingList, $baseArray)
	{
		if (CheckSerializedData($idFileDeletingList)
			&& ($serialisedValue = @unserialize($idFileDeletingList)) !== false)
		{
			$idFileDeletingList = $serialisedValue;
		}
		else
		{
			$idFileDeletingList = explode(';', $idFileDeletingList);
		}

		foreach ($idFileDeletingList as $idDelete)
		{
			$key = array_search($idDelete, $baseArray);
			if ($key !== false)
			{
				unset($baseArray[$key]);
			}
		}
		$newValue = serialize($baseArray);
		return $newValue;
	}
}
