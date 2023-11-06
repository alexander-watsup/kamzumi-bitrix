<?php

use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock as HL;
use \Bitrix\Main\SystemException;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Engine\ActionFilter;
use \Bitrix\Main\Engine\Contract\Controllerable;

defined('B_PROLOG_INCLUDED') || die();

if (!Loader::includeModule('redsign.b2bportal'))
	return;

class B2bPortalCatalogFilterComponent extends \Redsign\B2BPortal\Component\CatalogFilter implements Controllerable
{

	public $IBLOCK_ID        = 0;
	public $SECTION_ID       = 0;
	public $FILTER_NAME      = '';
	public $SAFE_FILTER_NAME = '';

	protected static $catalogIncluded = null;
	protected static $iblockIncluded  = null;

	/**
	 * standart bitrix function for ajax treatment
	 *
	 * @return array
	 */
	public function configureActions(): array
	{
		return [
			'tagssearch' => [
				'prefilters' => [
					new ActionFilter\Csrf(),
				],
			],
		];
	}

	/**
	 * standart bitrix function
	 *
	 * @param array $params
	 * @return array
	 */
	public function onPrepareComponentParams($params): array
	{
		$params = parent::onPrepareComponentParams($params);

		$params['IBLOCK_ID']    = (int) $params['IBLOCK_ID'];
		$params['SECTION_ID']   = (int) $params['SECTION_ID'];

		if (self::$iblockIncluded === null)
		{
			self::$iblockIncluded = Loader::includeModule('iblock');
		}

		if ($params['SECTION_ID'] <= 0 && self::$iblockIncluded)
		{
			$params['SECTION_ID'] = \CIBlockFindTools::GetSectionID(
				$params['SECTION_ID'],
				$params['SECTION_CODE'],
				[
					'GLOBAL_ACTIVE' => 'Y',
					'IBLOCK_ID' => $params['IBLOCK_ID'],
				]
			);
			if (!$params['SECTION_ID'] && strlen($params['SECTION_CODE_PATH']) > 0)
			{
				$params['SECTION_ID'] = \CIBlockFindTools::GetSectionIDByCodePath(
					$params['IBLOCK_ID'],
					$params['SECTION_CODE_PATH']
				);
			}
		}

		$params['PRICE_CODE']       = is_array($params['PRICE_CODE']) ? $params['PRICE_CODE'] : [];
		// $params['CONVERT_CURRENCY'] = $params['CONVERT_CURRENCY'] === 'Y';
		// $params['CURRENCY_ID']      = trim($params['CURRENCY_ID']);
		// if ($arParams['CURRENCY_ID'] == '')
		// {
		// 	$arParams['CONVERT_CURRENCY'] = false;
		// }
		// elseif (!$arParams['CONVERT_CURRENCY'])
		// {
		// 	$arParams['CURRENCY_ID'] = '';
		// }

		$this->arResult['FORM_ACTION'] = $params['AJAX_URL'];

		return $params;
	}

	/**
	 * standart bitrix function for components
	 *
	 * @return void
	 */
	public function executeComponent(): void
	{
		$this->IBLOCK_ID        = $this->arParams['IBLOCK_ID'];
		$this->SECTION_ID       = $this->arParams['SECTION_ID'];
		$this->FILTER_NAME      = $this->arParams['FILTER_NAME'];
		$this->SAFE_FILTER_NAME = htmlspecialcharsbx($this->FILTER_NAME);

		if ($this->SECTION_ID < 1)
		{
			$this->SECTION_ID = false;
		}

		$this->addResult();

		$this->makeFilter();

		$this->includeComponentTemplate();
	}

	/**
	 * fill arResult array
	 *
	 * @return void
	 */
	public function addResult(): void
	{
		$this->addResultFields();

		$this->addResultProperties();

		$this->addResultPrices();

		$this->addResultHtmlValues();

		$this->arResult['FIELDS']     = $this->fields;
		$this->arResult['PROPERTIES'] = $this->properties;
		$this->arResult['PRICES']     = $this->prices;
	}

	/**
	 * add arResult fields
	 *
	 * @return void
	 */
	public function addResultFields(): void
	{
		$this->fields = [];

		if (in_array('SUBSECTIONS', $this->arParams['FIELD_CODE']))
		{
			$this->addSectionItem();
		}

		if (in_array('ELEMENT_NAME', $this->arParams['FIELD_CODE']))
		{
			$this->addNameItem();
		}

		if (in_array('ACTIVE_FROM', $this->arParams['FIELD_CODE']))
		{
			$this->addActiveFromItem();
		}

		if (in_array('ACTIVE_TO', $this->arParams['FIELD_CODE']))
		{
			$this->addActiveToItem();
		}
	}

	/**
	 * add section field for arResult
	 *
	 * @return void
	 */
	public function addSectionItem(): void
	{
		$id           = 'SUBSECTIONS';
		$code         = $id;
		$name         = Loc::getMessage('RS.B2BPORTAL.CATALOG_FILTER.FILTER.'.$id);
		$propertyType = 'S';
		$controlId    = $this->SAFE_FILTER_NAME.'_'.$id;
		$controlName  = $controlId;

		$item = [
			'ID' => $id,
			'IBLOCK_ID' => $this->IBLOCK_ID,
			'CODE' => $code ?: $id,
			'~NAME' => $name,
			'NAME' => htmlspecialcharsEx($name),
			'PROPERTY_TYPE' => $propertyType,
			'CONTROL_ID' => $controlId,
			'CONTROL_NAME' => $controlName,
		];

		$this->fields[$id] = $item;
	}

	/**
	 * add element name field for arResult
	 *
	 * @return void
	 */
	public function addNameItem(): void
	{
		$id           = 'ELEMENT_NAME';
		$code         = $id;
		$name         = Loc::getMessage('RS.B2BPORTAL.CATALOG_FILTER.FILTER.'.$id);
		$propertyType = 'S';
		$controlId    = $this->SAFE_FILTER_NAME.'_'.$id;
		$controlName  = $controlId;

		$item = [
			'ID' => $id,
			'IBLOCK_ID' => $this->IBLOCK_ID,
			'CODE' => $code ?: $id,
			'~NAME' => $name,
			'NAME' => htmlspecialcharsEx($name),
			'PROPERTY_TYPE' => $propertyType,
			'CONTROL_ID' => $controlId,
			'CONTROL_NAME' => $controlName,
		];

		$this->fields[$id] = $item;
	}

	/**
	 * add active from field for arResult
	 *
	 * @return void
	 */
	public function addActiveFromItem(): void
	{
		$id           = 'ACTIVE_FROM';
		$code         = $id;
		$name         = Loc::getMessage('RS.B2BPORTAL.CATALOG_FILTER.FILTER.'.$id);
		$propertyType = 'S:Date';

		$item = [
			'ID' => $id,
			'IBLOCK_ID' => $this->IBLOCK_ID,
			'CODE' => $code ?: $id,
			'~NAME' => $name,
			'NAME' => htmlspecialcharsEx($name),
			'PROPERTY_TYPE' => $propertyType,
		];

		$minID = $this->SAFE_FILTER_NAME.'_'.$id.'_MIN';
		$maxID = $this->SAFE_FILTER_NAME.'_'.$id.'_MAX';

		$item['VALUES'] = [
			'MIN' => [
				'CONTROL_ID' => $minID,
				'CONTROL_NAME' => $minID,
			],
			'MAX' => [
				'CONTROL_ID' => $maxID,
				'CONTROL_NAME' => $maxID,
			],
		];

		$this->fields[$id] = $item;
	}

	/**
	 * add active to field for arResult
	 *
	 * @return void
	 */
	public function addActiveToItem(): void
	{
		$id           = 'ACTIVE_TO';
		$code         = $id;
		$name         = Loc::getMessage('RS.B2BPORTAL.CATALOG_FILTER.FILTER.'.$id);
		$propertyType = 'S:Date';

		$item = [
			'ID' => $id,
			'IBLOCK_ID' => $this->IBLOCK_ID,
			'CODE' => $code ?: $id,
			'~NAME' => $name,
			'NAME' => htmlspecialcharsEx($name),
			'PROPERTY_TYPE' => $propertyType,
		];

		$minID = $this->SAFE_FILTER_NAME.'_'.$id.'_MIN';
		$maxID = $this->SAFE_FILTER_NAME.'_'.$id.'_MAX';

		$item['VALUES'] = [
			'MIN' => [
				'CONTROL_ID' => $minID,
				'CONTROL_NAME' => $minID,
			],
			'MAX' => [
				'CONTROL_ID' => $maxID,
				'CONTROL_NAME' => $maxID,
			],
		];

		$this->fields[$id] = $item;
	}

	/**
	 * add arResult prioperties
	 *
	 * @return void
	 */
	public function addResultProperties(): void
	{
		$this->properties = [];

		if (!empty($this->arParams['PROPERTY_CODE']))
		{
			$this->addPropertyItems();
		}
	}

	/**
	 * add property for arResult
	 *
	 * @return void
	 */
	public function addPropertyItems(): void
	{
		if (self::$iblockIncluded === null)
		{
			self::$iblockIncluded = Loader::includeModule('iblock');
		}

		if (self::$iblockIncluded)
		{
			$arOrder    = ['SORT' => 'ASC', 'NAME' => 'ASC'];
			$arFilter   = [
				'ACTIVE' => 'Y',
				'IBLOCK_ID' => $this->IBLOCK_ID,
			];
			$rsProps = \CIBlockProperty::GetList($arOrder, $arFilter);
			while ($arProperty = $rsProps->GetNext())
			{
				if (!in_array($arProperty['CODE'], $this->arParams['PROPERTY_CODE']))
				{
					continue;
				}

				$computedType                        = $this->getComputedTypeByProperty($arProperty);
				$this->properties[$arProperty['ID']] = [
					'ID' => $arProperty['ID'],
					'IBLOCK_ID' => $arProperty['IBLOCK_ID'],
					'CODE' => $arProperty['CODE'],
					'~NAME' => $arProperty['NAME'],
					'NAME' => htmlspecialcharsEx($arProperty['NAME']),
					'PROPERTY_TYPE' => $arProperty['PROPERTY_TYPE'],
					'COMPUTED_TYPE' => $computedType,
					'DISPLAY_TYPE' => $this->getDisplayTypeByPropertyComputedType($computedType),
					'USER_TYPE_SETTINGS' =>  $arProperty['USER_TYPE_SETTINGS'],
					// 'VALUES' => [],
				];

				if ($computedType == 'N' || $computedType == 'S:Date')
				{
					$minID = $this->SAFE_FILTER_NAME.'_'.$arProperty['ID'].'_MIN';
					$maxID = $this->SAFE_FILTER_NAME.'_'.$arProperty['ID'].'_MAX';

					$this->properties[$arProperty['ID']]['VALUES'] = [
						'MIN' => [
							'CONTROL_ID' => $minID,
							'CONTROL_NAME' => $minID,
						],
						'MAX' => [
							'CONTROL_ID' => $maxID,
							'CONTROL_NAME' => $maxID,
						],
					];
				}
				else
				{
					$controlKey                                          = $this->SAFE_FILTER_NAME.'_'.$arProperty['ID'];
					$this->properties[$arProperty['ID']]['CONTROL_ID']   = $controlKey;
					$this->properties[$arProperty['ID']]['CONTROL_NAME'] = $controlKey;
				}
			}
		}
	}

	/**
	 * add arResult prices
	 *
	 * @return void
	 */
	public function addResultPrices(): void
	{
		$this->prices = [];

		if (!empty($this->arParams['PRICE_CODE']))
		{
			$this->addPricesItems($this->IBLOCK_ID);
		}
	}

	/**
	 * add price for arResult
	 *
	 * @return void
	 */
	public function addPricesItems(): void
	{
		if (empty($this->arParams['PRICE_CODE']))
		{
			return;
		}

		if (self::$catalogIncluded === null)
		{
			self::$catalogIncluded = Loader::includeModule('catalog');
		}
		if (self::$catalogIncluded)
		{
			$rsPrice = CCatalogGroup::GetList(
				['SORT' => 'ASC', 'ID' => 'ASC'],
				['=NAME' => $this->arParams['PRICE_CODE']],
				false,
				false,
				['ID', 'NAME', 'NAME_LANG', 'CAN_ACCESS', 'CAN_BUY']
			);
			while ($arPrice = $rsPrice->Fetch())
			{
				if (!$arPrice['CAN_ACCESS'] == 'Y' && !$arPrice['CAN_BUY'] == 'Y')
				{
					continue;
				}
				$arPrice['NAME_LANG'] = (string) $arPrice['NAME_LANG'];
				if ($arPrice['NAME_LANG'] === '')
				{
					$arPrice["NAME_LANG"] = $arPrice["NAME"];
				}

				$minID = $this->SAFE_FILTER_NAME.'_P'.$arPrice['ID'].'_MIN';
				$maxID = $this->SAFE_FILTER_NAME.'_P'.$arPrice['ID'].'_MAX';

				$this->prices[$arPrice['NAME']] = [
					'ID' => $arPrice['ID'],
					'CODE' => $arPrice['NAME'],
					'~NAME' => $arPrice["NAME_LANG"],
					'NAME' => htmlspecialcharsbx($arPrice['NAME_LANG']),
					'DISPLAY_TYPE' => $this->getDisplayTypeByPropertyComputedType('PRICE'),
					'VALUES' => [
						'MIN' => [
							'CONTROL_ID' => $minID,
							'CONTROL_NAME' => $minID,
						],
						'MAX' => [
							'CONTROL_ID' => $maxID,
							'CONTROL_NAME' => $maxID,
						],
					],
					'ENCODED_ID' => md5($arPrice['NAME']),
				];
			}
		}
	}

	/**
	 * make computed property type by property type and user type
	 *
	 * @param array $arProperty
	 * @return string
	 */
	public function getComputedTypeByProperty($arProperty): string
	{
		return $arProperty['PROPERTY_TYPE'].(!empty($arProperty['USER_TYPE']) ? ':'.$arProperty['USER_TYPE'] : '');
	}

	/**
	 * get dispaly type by computed type
	 *
	 * @param string $computedType
	 * @return string
	 */
	public function getDisplayTypeByPropertyComputedType($computedType): string
	{
		$displayType = 'STRING';

		switch ($computedType)
		{
			case 'N':
			case 'PRICE':
				$displayType = 'NUMBER_RANGE';
				break;
			case 'L':
				// $displayType = 'CHECKBOX';
				$displayType = 'TAGSSEARCH';
				break;
			case 'S:directory':
				$displayType = 'TAGSSEARCH';
				break;
			case 'S:Date':
				$displayType = 'DATE_RANGE';
				break;
		}

		return $displayType;
	}

	/**
	 * fill html values
	 *
	 * @return void
	 */
	public function addResultHtmlValues(): void
	{
		$this->addHtmlValuesFields();
		$this->addDataFields();

		$this->addHtmlValuesProperties();
		$this->addDataProperties();

		$this->addHtmlValuesPrices();
	}

	/**
	 * fill html values for fields
	 *
	 * @return void
	 */
	public function addHtmlValuesFields(): void
	{
		if (empty($this->fields))
		{
			return;
		}

		foreach ($this->fields as $id => $arField)
		{
			if ($value = $this->request->get($arField['CONTROL_NAME']))
			{
				$this->fields[$id]['HTML_VALUE'] = $value;
			}
			else
			{
				$this->fields[$id]['HTML_VALUE'] = '';
			}
		}
	}

	/**
	 * fill data for fields
	 *
	 * @return void
	 */
	public function addDataFields(): void
	{
		$this->addDataFieldsSubsections();
		$this->addDataFieldsElementName();
		$this->addDataFieldsActiveFromName();
		$this->addDataFieldsActiveToName();
	}

	/**
	 * fill data for section field
	 *
	 * @return void
	 */
	public function addDataFieldsSubsections(): void
	{
		if (empty($this->fields) || empty($this->fields['SUBSECTIONS']['HTML_VALUE']))
		{
			return;
		}

		if (self::$iblockIncluded === null)
		{
			self::$iblockIncluded = Loader::includeModule('iblock');
		}

		if (!self::$iblockIncluded)
		{
			return;
		}

		$arSections = [];
		if (!empty($this->fields['SUBSECTIONS']['HTML_VALUE']))
		{
			$arOrder    = [];
			$arFilter   = [
				'IBLOCK_ID' => $this->IBLOCK_ID,
				'=ID' => $this->fields['SUBSECTIONS']['HTML_VALUE'],
			];
			$arSelect = ['ID', 'IBLOCK_ID', 'NAME'];

			$sectionIterator = \CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
			while ($arSection = $sectionIterator->Fetch())
			{
				$arSections[$arSection['ID']] = $arSection;
			}
		}

		$this->fields['SUBSECTIONS']['DATA'] = [
			'ITEMS' => $arSections,
		];
	}

	/**
	 * fill data for element name field
	 *
	 * @return void
	 */
	public function addDataFieldsElementName(): void
	{
	}

	/**
	 * fill data for date from field
	 *
	 * @return void
	 */
	public function addDataFieldsActiveFromName(): void
	{
		if (!empty($this->fields['ACTIVE_FROM']['VALUES']) && is_array($this->fields['ACTIVE_FROM']['VALUES']))
		{
			foreach ($this->fields['ACTIVE_FROM']['VALUES'] as $id2 => $arData)
			{
				if ($value = $this->request->get($arData['CONTROL_NAME']))
				{
					$this->fields['ACTIVE_FROM']['VALUES'][$id2]['HTML_VALUE'] = $value;
				}
				else
				{
					$this->fields['ACTIVE_FROM']['VALUES'][$id2]['HTML_VALUE'] = '';
				}
			}
		}
	}

	/**
	 * fill data for date from field
	 *
	 * @return void
	 */
	public function addDataFieldsActiveToName(): void
	{
		if (!empty($this->fields['ACTIVE_TO']['VALUES']) && is_array($this->fields['ACTIVE_TO']['VALUES']))
		{
			foreach ($this->fields['ACTIVE_TO']['VALUES'] as $id2 => $arData)
			{
				if ($value = $this->request->get($arData['CONTROL_NAME']))
				{
					$this->fields['ACTIVE_TO']['VALUES'][$id2]['HTML_VALUE'] = $value;
				}
				else
				{
					$this->fields['ACTIVE_TO']['VALUES'][$id2]['HTML_VALUE'] = '';
				}
			}
		}
	}

	/**
	 * fill html values for properties
	 *
	 * @return void
	 */
	public function addHtmlValuesProperties(): void
	{
		if (empty($this->properties))
		{
			return;
		}

		foreach ($this->properties as $id => $arProperty)
		{
			if (!empty($arProperty['VALUES']) && is_array($arProperty['VALUES']))
			{
				foreach ($arProperty['VALUES'] as $id2 => $arData)
				{
					if ($value = $this->request->get($arData['CONTROL_NAME']))
					{
						$this->properties[$id]['VALUES'][$id2]['HTML_VALUE'] = $value;
					}
					else
					{
						$this->properties[$id]['VALUES'][$id2]['HTML_VALUE'] = '';
					}
				}
			}
			else
			{
				if ($value = $this->request->get($arProperty['CONTROL_NAME']))
				{
					$this->properties[$id]['HTML_VALUE'] = $value;
				}
				else
				{
					$this->properties[$id]['HTML_VALUE'] = '';
				}
			}
		}
	}

	/**
	 * fill data for properties
	 *
	 * @return void
	 */
	public function addDataProperties(): void
	{
		if (empty($this->properties))
		{
			return;
		}

		if (self::$iblockIncluded === null)
		{
			self::$iblockIncluded = Loader::includeModule('iblock');
		}

		if (!self::$iblockIncluded)
		{
			return;
		}

		$arPropertyIds = [
			'L' => [],
			'S:directory' => [],
		];
		foreach ($this->properties as $id => $arProperty)
		{
			if (empty($arProperty['HTML_VALUE']))
				continue;

			$this->properties[$id]['DATA'] = [
				'ITEMS' => [],
			];

			switch ($arProperty['COMPUTED_TYPE'])
			{
				case 'L':
					$arPropertyIds['L'] = array_merge($arPropertyIds['L'], $arProperty['HTML_VALUE']);
					break;
				case 'S:directory':
					$this->properties[$id]['DATA']['ITEMS'] = $this->getHLValuesByProperty($arProperty);
					break;
			}
		}

		if (!empty($arPropertyIds['L']))
		{
			$arAllItems = [];
			$arFilter = [
				'ID' => $arPropertyIds['L'],
			];
			$arAllItems = $this->getEnumValuesByFilter($arFilter);
			foreach ($arAllItems as $arItem)
			{
				$this->properties[$arItem['PROPERTY_ID']]['DATA']['ITEMS'][$arItem['ID']] = $arItem;
			}
		}
	}

	public function getEnumValuesByFilter($arFilter): array
	{
		$arOrder = [];
		$arItems = [];
		$res = \CIBlockPropertyEnum::GetList($arOrder, $arFilter);
		while ($row = $res->fetch())
		{
			$arItems[$row['ID']] = $row;
		}

		return $arItems;
	}

	public function getHLValuesByProperty($arProperty): array
	{
		$arItems = [];
		$sTableName = $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'];

		if (!Loader::IncludeModule('highloadblock'))
		{
			throw new SystemException('Failed module hl loading');
		}

		if (empty($sTableName))
		{
			throw new SystemException('Failed table name');
		}

		$hlblock = HL\HighloadBlockTable::getRow([
			'filter' => [
				'=TABLE_NAME' => $sTableName
			],
		]);

		if (!$hlblock)
		{
			throw new SystemException('Can\'t find hl by table name');
		}

		$entity      = HL\HighloadBlockTable::compileEntity($hlblock);
		$entityClass = $entity->getDataClass();

		$arRes = $entityClass::getList([
			'filter' => [
				'UF_XML_ID' => $arProperty['HTML_VALUE'],
			],
		]);
		foreach ($arRes as $row)
		{
			$arItems[$row['UF_XML_ID']] = $row;
		}

		return $arItems;
	}

	/**
	 * fill html values for prices
	 *
	 * @return void
	 */
	public function addHtmlValuesPrices(): void
	{
		if (empty($this->prices))
		{
			return;
		}

		foreach ($this->prices as $id => $arPrice)
		{
			if (empty($arPrice['VALUES']) || !is_array($arPrice['VALUES']))
			{
				continue;
			}

			foreach ($arPrice['VALUES'] as $priceId => $arData)
			{
				if ($value = $this->request->get($arData['CONTROL_NAME']))
				{
					$this->prices[$id]['VALUES'][$priceId]['HTML_VALUE'] = $value;
				}
				else
				{
					$this->prices[$id]['VALUES'][$priceId]['HTML_VALUE'] = '';
				}
			}
		}
	}

	/**
	 * make arrFilter for catalog.section component
	 *
	 * @return void
	 */
	public function makeFilter(): void
	{
		$this->makeFilterCommon();
		$this->makeFilterFields();
		$this->makeFilterProperties();
		$this->makeFilterPrices();
	}

	/**
	 * fill filter, common fields
	 *
	 * @return void
	 */
	public function makeFilterCommon(): void
	{
		global ${$this->FILTER_NAME};

		if (!is_array(${$this->FILTER_NAME}))
		{
			${$this->FILTER_NAME} = [];
		}

		$arFilter = [
			'INCLUDE_SUBSECTIONS' => $this->arParams['INCLUDE_SUBSECTIONS'] != 'N' ? 'Y' : 'N',
		];

		${$this->FILTER_NAME} = array_merge(${$this->FILTER_NAME}, $arFilter);
	}

	/**
	 * fill filter, fields
	 *
	 * @return void
	 */
	public function makeFilterFields(): void
	{
		global ${$this->FILTER_NAME};

		if (empty($this->fields))
		{
			return;
		}

		$arFilter = [];

		if (!is_array(${$this->FILTER_NAME}))
		{
			${$this->FILTER_NAME} = [];
		}

		// SUBSECTIONS
		if (!empty($this->fields['SUBSECTIONS']['HTML_VALUE']))
		{
			foreach ($this->fields['SUBSECTIONS']['HTML_VALUE'] as $value)
			{
				if (empty($value) || $value == '')
				{
					continue;
				}
				$arFilter['SECTION_ID'][] = $value;
			}

		}
		elseif (!empty($this->SECTION_ID) && ($this->SECTION_ID > 0 || $this->arParams['SHOW_ALL_WO_SECTION'] !== 'Y'))
		{
			$arFilter['SECTION_ID'] = $this->SECTION_ID;
		}

		// ELEMENT_NAME
		if (!empty($this->fields['ELEMENT_NAME']['HTML_VALUE']))
		{
			$arFilter['NAME'] = '%'.$this->fields['ELEMENT_NAME']['HTML_VALUE'].'%';
		}
		${$this->FILTER_NAME} = array_merge(${$this->FILTER_NAME}, $arFilter);

		// active from
		if (!empty($this->fields['ACTIVE_FROM']['VALUES']) && is_array($this->fields['ACTIVE_FROM']['VALUES']))
		{
			foreach ($this->fields['ACTIVE_FROM']['VALUES'] as $key => $arData)
			{
				if (empty($arData['HTML_VALUE']))
				{
					continue;
				}
				if ($key == 'MIN')
				{
					$arFilter['>=DATE_ACTIVE_FROM'] = ConvertDateTime($arData['HTML_VALUE']);
				}
				elseif ($key == 'MAX')
				{
					$arFilter['<=DATE_ACTIVE_FROM'] = ConvertDateTime($arData['HTML_VALUE'].' 23:59:59');
				}
			}
		}
		${$this->FILTER_NAME} = array_merge(${$this->FILTER_NAME}, $arFilter);

		// active to
		if (!empty($this->fields['ACTIVE_TO']['VALUES']) && is_array($this->fields['ACTIVE_TO']['VALUES']))
		{
			foreach ($this->fields['ACTIVE_TO']['VALUES'] as $key => $arData)
			{
				if (empty($arData['HTML_VALUE']))
				{
					continue;
				}
				if ($key == 'MIN')
				{
					$arFilter['>=DATE_ACTIVE_TO'] = ConvertDateTime($arData['HTML_VALUE'], 'YYYY-MM-DD');
				}
				elseif ($key == 'MAX')
				{
					$arFilter['<=DATE_ACTIVE_TO'] = ConvertDateTime($arData['HTML_VALUE'], 'YYYY-MM-DD');
				}
			}
		}
		${$this->FILTER_NAME} = array_merge(${$this->FILTER_NAME}, $arFilter);
	}

	/**
	 * fill filter, properties
	 *
	 * @return void
	 */
	public function makeFilterProperties(): void
	{
		global ${$this->FILTER_NAME};

		if (empty($this->properties))
		{
			return;
		}

		$arFilter = [];

		if (!is_array(${$this->FILTER_NAME}))
		{
			${$this->FILTER_NAME} = [];
		}

		foreach ($this->properties as $arProperty)
		{
			if (empty($arProperty['HTML_VALUE']) && empty($arProperty['VALUES']))
			{
				continue;
			}

			if (!empty($arProperty['VALUES']) && is_array($arProperty['VALUES']))
			{
				foreach ($arProperty['VALUES'] as $key => $arData)
				{
					if (empty($arData['HTML_VALUE']))
					{
						continue;
					}
					if ($key == 'MIN')
					{
						$arFilter['>='.$this->getFilterPropertyName($arProperty)] = ConvertDateTime($arData['HTML_VALUE'], 'YYYY-MM-DD');
					}
					elseif ($key == 'MAX')
					{
						$arFilter['<='.$this->getFilterPropertyName($arProperty)] = ConvertDateTime($arData['HTML_VALUE'], 'YYYY-MM-DD');
					}
				}
			}
			else
			{
				$arFilter[$this->getFilterPropertyName($arProperty)] = $this->getFilterPropertyValue($arProperty);
			}
		}

		${$this->FILTER_NAME} = array_merge(${$this->FILTER_NAME}, $arFilter);
	}

	/**
	 * function return property_key_value for arrFilter by property type
	 *
	 * @param array $arProperty
	 * @return string
	 */
	public function getFilterPropertyName($arProperty): string
	{
		if (empty($arProperty))
		{
			return '';
		}

		$return = 'PROPERTY_'.$arProperty['ID'];

		return $return;
	}

	/**
	 * function return property value for arrFilter by property type
	 *
	 * @param array $arProperty
	 * @return void
	 */
	public function getFilterPropertyValue($arProperty)
	{
		if (empty($arProperty))
		{
			return '';
		}

		$return = '%'.$arProperty['HTML_VALUE'].'%';

		switch ($arProperty['COMPUTED_TYPE'])
		{
			case 'L':
			case 'S:directory':
				$return = $arProperty['HTML_VALUE'];
				break;
		}

		return $return;
	}

	/**
	 * fill filter, prices
	 *
	 * @return void
	 */
	public function makeFilterPrices(): void
	{
		global ${$this->FILTER_NAME};

		if (empty($this->prices))
		{
			return;
		}

		$arFilter = [];

		if (!is_array(${$this->FILTER_NAME}))
		{
			${$this->FILTER_NAME} = [];
		}

		foreach ($this->prices as $id => $arPrice)
		{
			if (empty($arPrice['VALUES']))
			{
				continue;
			}
			foreach ($arPrice['VALUES'] as $key => $arData)
			{
				if (empty($arData['HTML_VALUE']))
				{
					continue;
				}
				if ($key == 'MIN')
				{
					$arFilter['>=CATALOG_PRICE_SCALE_'.$arPrice['ID']] = $arData['HTML_VALUE'];
				}
				elseif ($key == 'MAX')
				{
					$arFilter['<=CATALOG_PRICE_SCALE_'.$arPrice['ID']] = $arData['HTML_VALUE'];
				}
			}
		}

		${$this->FILTER_NAME} = array_merge(${$this->FILTER_NAME}, $arFilter);
	}

	/**
	 * inner functiomn for runComponentAjax, return section list by query
	 *
	 * @param string $query
	 * @param array $params
	 * @return array
	 */
	public function searchSections($query, $params): array
	{
		if (!Loader::includeModule('iblock'))
		{
			throw new SystemException('Failed module iblock loading');
		}

		if (empty($params['iblockId']))
		{
			throw new SystemException('Failed input parameters');
		}

		$arSections = [];
		$arOrder    = [];
		$arFilter   = [
			'IBLOCK_ID' => $params['iblockId'],
			'ACTIVE' => 'Y',
			'NAME' => "%$query%",
		];
		if (!empty($params['sectionId']))
		{
			$arFilter['SECTION_ID'] = $params['sectionId'];
		}
		if (!empty($params['excludeId']))
		{
			$arFilter['!ID'] = $params['excludeId'];
		}
		$arSelect = [
			'ID',
			'NAME',
			'IBLOCK_ID',
			'ACTIVE',
		];
		$arNavStartParams = [
			'nTopCount' => 10,
		];
		$res = \CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect, $arNavStartParams);
		while ($row = $res->fetch())
		{
			$arSections[] = [
				'id' => $row['ID'],
				'name' => $row['NAME'],
			];
		}

		if (!empty($arSections))
		{
			$arReturn = [
				'items' => $arSections
			];
		}
		else
		{
			$arReturn = [
				'items' => [],
			];
		}

		return $arReturn;
	}

	/**
	 * return property list values
	 *
	 * @param string $query
	 * @param array $arProperty
	 * @return void
	 */
	public function getVariantsByPropL($query, $params, $arProperty)
	{
		$arEnum     = [];
		$arOrder    = ['DEF' => 'DESC', 'SORT' => 'ASC', 'NAME' => 'ASC'];
		$arFilter   = [
			'PROPERTY_ID' => $arProperty['ID'],
			'VALUE' => "%$query%",
		];
		if (!empty($params['excludeId']))
		{
			$arFilter['!ID'] = $params['excludeId'];
		}
		$res = \CIBlockPropertyEnum::GetList($arOrder, $arFilter);
		while ($row = $res->fetch())
		{
			$arEnum[] =  [
				'id' => $row['ID'],
				'name' => $row['VALUE'],
			];
		}

		return $arEnum;
	}

	/**
	 * return property highload values
	 *
	 * @param string $query
	 * @param array $arProperty
	 * @return void
	 */
	public function getVariantsByPropHL($query, $params, $arProperty): array
	{
		$arReturn   = [];
		$sTableName = $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'];

		if (!Loader::IncludeModule('highloadblock'))
		{
			throw new SystemException('Failed module hl loading');
		}

		if (empty($sTableName))
		{
			throw new SystemException('Failed table name');
		}

		$hlblock = HL\HighloadBlockTable::getRow([
			'filter' => [
				'=TABLE_NAME' => $sTableName
			],
		]);

		if (!$hlblock)
		{
			throw new SystemException('Can\'t find hl by table name');
		}

		$entity      = HL\HighloadBlockTable::compileEntity($hlblock);
		$entityClass = $entity->getDataClass();

		$arFilter = [
			'UF_NAME' => "%$query%",
		];
		if (!empty($params['excludeId']))
		{
			$arFilter['!UF_XML_ID'] = $params['excludeId'];
		}
		$arRes = $entityClass::getList([
			'filter' => $arFilter,
		]);
		foreach ($arRes as $row)
		{
			$arReturn[] = [
				'id' => $row['UF_XML_ID'],
				'name' => $row['UF_NAME'],
			];
		}

		return $arReturn;
	}

	/**
	 * return property values by property type
	 *
	 * @param string $query
	 * @param array $arProperty
	 * @return array
	 */
	public function getVariantsByProp($query, $params, $arProperty): array
	{
		$arReturn = [];

		switch ($arProperty['COMPUTED_TYPE'])
		{
			case 'L':
				$arReturn = $this->getVariantsByPropL($query, $params, $arProperty);
				break;
			case 'S:directory':
				$arReturn = $this->getVariantsByPropHL($query, $params, $arProperty);
				break;
		}

		return $arReturn;
	}

	/**
	 * inner functiomn for runComponentAjax, return property values by query
	 *
	 * @param string $query
	 * @param array $params
	 * @return array
	 */
	public function searchProperty($query, $params): array
	{
		$arReturn = [];

		if (!Loader::includeModule('iblock'))
		{
			throw new SystemException('Failed module iblock loading');
		}

		if (empty($params['iblockId']) || empty($params['propertyId']))
		{
			throw new SystemException('Failed input parameters');
		}

		$arVariants     = [];
		$arAllowedTypes = ['S:directory', 'L'];
		$arOrder        = ['SORT' => 'ASC', 'NAME' => 'ASC'];
		$arFilter       = [
			'ACTIVE' => 'Y',
			'ID' => $params['propertyId'],
			'IBLOCK_ID' => $params['iblockId'],
		];
		$rsProps = \CIBlockProperty::GetList($arOrder, $arFilter);
		if ($arProperty = $rsProps->GetNext())
		{
			$arProperty['COMPUTED_TYPE'] = $this->getComputedTypeByProperty($arProperty);
			if (!in_array($arProperty['COMPUTED_TYPE'], $arAllowedTypes))
			{
				throw new SystemException('Not allowed property type - '.$arProperty['COMPUTED_TYPE']);
			}
			else
			{
				$arVariants = $this->getVariantsByProp($query, $params, $arProperty);
			}
		}

		if (!empty($arVariants))
		{
			$arReturn = [
				'items' => $arVariants
			];
		}
		else
		{
			$arReturn = [
				'items' => [],
			];
		}

		return $arReturn;
	}

	/**
	 * action functiomn for runComponentAjax, return values by query
	 *
	 * @param string $query
	 * @param array $params
	 * @return array
	 */
	public function tagssearch($query, $params): array
	{
		$arReturn = [];
		$arAllowedEntities = ['subsections', 'property'];

		if (!in_array($params['entity'], $arAllowedEntities))
		{
			throw new SystemException('Not allowed tagssearch entity - '.$params['entity']);
		}

		switch ($params['entity'])
		{
			case 'subsections':
				$arReturn = $this->searchSections($query, $params);
				break;
			case 'property':
				$arReturn = $this->searchProperty($query, $params);
				break;
		}
		
		return $arReturn;
	}

	/**
	 * action functiomn for runComponentAjax, return values by query
	 *
	 * @param string $query
	 * @param array $params
	 * @return array
	 */
	public function tagssearchAction($query, $params): array
	{
		try
		{
			$arReturn = $this->tagssearch($query, $params);
		}
		catch (SystemException $e)
		{
			$arReturn = [
				'errors' => [
					$e->getMessage(),
				],
			];
		}

		return $arReturn;
	}

}
