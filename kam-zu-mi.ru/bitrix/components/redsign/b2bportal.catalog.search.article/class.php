<?php

use Bitrix\ 
{
	Main\Context,
	Main\Loader,
	Main\SystemException,
	Main\Localization\Loc,
	Main\Engine\ActionFilter,
	Main\Engine\Contract\Controllerable
};

Loc::loadMessages(__FILE__);

class RedsignB2BPortalCatalogSearchArticle extends CBitrixComponent  implements Controllerable
{
	protected const DEFAULT_QUERY_MIN_STLEN = 3;
	protected const DEFAULT_PROP_CODE = 'CML2_ARTICLE';
	protected const DEFAULT_SHOW_COUNT = 20;

	/** @var int */
	protected $iblockId;

	/** @var int */
	protected $offersIblockId;

	/** @var array */
	protected $items = [];
	
	/** @var array */
	protected $measures = [];

	/**
	 * @return array
	 */
	public function configureActions(): array
	{
		return [
			'search' => [
				'prefilters' => [
					new ActionFilter\Csrf(),
				]
			],
			'addtobasket' => [
				'prefilters' => [
					new ActionFilter\Csrf(),
				]
			]
		];
	}

	/**
	 * @return array
	 */
	protected function listKeysSignedParameters(): array
	{
		return [
			'IBLOCK_ID',
			'PROP_CODE',
			'OFFERS_PROP_CODE',
			'PROPS',
			'PRICES'
		];
	}

	/**
	 * @param array $arParams
	 * 
	 * @return array
	 */
	public function onPrepareComponentParams(array $arParams): array
	{
		if (empty($arParams['MIN_STRLEN']) || $arParams['MIN_STRLEN'] < 0)
		{
			$arParams['MIN_STRLEN'] = self::DEFAULT_QUERY_MIN_STLEN;
		}

		if (empty($arParams['PROP_CODE']))
		{
			$arParams['PROP_CODE'] = self::DEFAULT_PROP_CODE;
		}

		if (empty($arParams['OFFERS_PROP_CODE']))
		{
			$arParams['OFFERS_PROP_CODE'] = self::DEFAULT_PROP_CODE;
		}

		if (empty($arParams['SHOW_COUNT']))
		{
			$arParams['SHOW_COUNT'] = self::DEFAULT_SHOW_COUNT;
		}

		if (!isset($arParams['CONVERT_CURRENCY']))
		{
			$arParams['CONVERT_CURRENCY'] = false;
		}
		else
		{
			$arParams['CONVERT_CURRENCY'] = $arParams['CONVERT_CURRENCY'] || $arParams['CONVERT_CURRENCY'] == 'Y';
		}

		if (!isset($arParams['CURRENCY_ID']))
		{
			$arParams['CURRENCY_ID'] = '';
		}

		$this->iblockId = $arParams['IBLOCK_ID'];
		$this->offersIblockId = $this->getOffersIblockId();

		return $arParams;
	}

	/**
	 * @throws SystemException
	 * 
	 * @return void
	 */
	protected function checkModules(): void
	{
		if (!Loader::includeModule('redsign.b2bportal'))
		{
			throw new SystemException(
				Loc::getMessage('RS_B2BPORTAL_BS_MODULE_NOT_INSTALLED')
			);
		}
		
		if (!Loader::includeModule('iblock'))
		{
			throw new SystemException(
				Loc::getMessage('RS_B2BPORTAL_BS_MODULE_IBLOCK_NOT_INSTALLED')
			);
		}

		if (!Loader::includeModule('catalog'))
		{
			throw new SystemException(
				Loc::getMessage('RS_B2BPORTAL_BS_MODULE_CATALOG_NOT_INSTALLED')
			);
		}
	}

	protected function getOffersIblockId() {
		
		if (Loader::includeModule('catalog'))
		{
			$iterator = \Bitrix\Catalog\CatalogIblockTable::getList(array(
				'select' => array('IBLOCK_ID'),
				'filter' => array('=PRODUCT_IBLOCK_ID' => $this->iblockId)
			));
	
			while ($row = $iterator->fetch())
			{
				return $row['IBLOCK_ID'];
			}
		}
		
		return false;
	}

	/**
	 * @param $arPrice
	 * 
	 * @return bool
	 */
	protected function filterPrices(array $arPrice): bool
	{
		return $arPrice['CAN_VIEW'] && $arPrice['CAN_BUY'];
	}

	/**
	 * @return array
	 */
	protected function loadPrices(): void
	{
		global $USER;

		if (!empty($this->arParams['PRICES']) && is_array($this->arParams['PRICES']))
		{
			$this->prices = array_filter(
				CIBlockPriceTools::GetCatalogPrices(0, $this->arParams['PRICES']), 
				array($this, 'filterPrices')
			);
		}
	}

	/**
	 * @return array[string] string 
	 */
	protected function getDefaultMeasure()
	{
		static $measureData = null;

		if (is_null($data))
		{
			$defaultMeasure = \CCatalogMeasure::getDefaultMeasure(true, true);
			$measureData = [
				'ID' => $defaultMeasure['ID'],
				'TITLE' => $defaultMeasure['SYMBOL_RUS']
			];
		}

		return $measureData;
	}

	/**
	 * @return void
	 */
	protected function loadMeasures(): void
	{
		
		$arIds = array_map(function ($item) {
			return $item['MEASURE'];
		}, $this->items);

		if (count($arIds) > 0)
		{
			$measureIterator = \CCatalogMeasure::getList(
				array(),
				array('@ID' => $arIds),
				false,
				false,
				array('ID', 'SYMBOL_RUS')
			);
	
			while ($measure = $measureIterator->GetNext())
			{
				$this->measures[$measure['ID']] = [
					'ID' => $measure['ID'],
					'TITLE' => $measure['SYMBOL_RUS']
				];
			}
		}

	}

	/**
	 * @param string $value
	 * 
	 * @return void
	 */
	protected function loadItemsByQuery(string $value): void
	{
		if ($this->offersIblockId)
		{
			$arFilter = ['LOGIC' => 'OR'];
			$arFilter[] = [
				'=IBLOCK_ID' => $this->iblockId,
				'%PROPERTY_'.$this->arParams['PROP_CODE'] => $value,
				'=CATALOG_TYPE' => \Bitrix\Catalog\ProductTable::TYPE_PRODUCT
			];
			$arFilter[] = [
				'=IBLOCK_ID' => $this->offersIblockId,
				'%PROPERTY_'.$this->arParams['OFFERS_PROP_CODE'] => $value,
				'=CATALOG_TYPE' => \Bitrix\Catalog\ProductTable::TYPE_OFFER
			];
		}
		else
		{
			$arFilter = [
				'=IBLOCK_ID' => $this->iblockId,
				'%PROPERTY_'.$this->arParams['PROP_CODE'] => $value,
				'=CATALOG_TYPE' => \Bitrix\Catalog\ProductTable::TYPE_PRODUCT
			];
		}

		$arSelect = [
			'ID', 'IBLOCK_ID', 'NAME', 'QUANTITY', 'MEASURE',
			'DETAIL_PAGE_URL', 'PROPERTY_'.$this->arParams['PROP_CODE']
		];

		foreach ($this->prices as $arPrice)
		{
			$arSelect[] = 'CATALOG_PRICE_'.$arPrice['ID'];
		}

		$res = CIBlockElement::GetList(
			['SORT' => 'ASC'],
			$arFilter,
			false,
			['nPageSize' => $this->arParams['SHOW_COUNT']],
			$arSelect
		);

		while ($arItem = $res->GetNext())
		{
			$this->items[] = $arItem;
		}
	}

	/**
	 * @return void
	 */
	protected function loadItemPrices()
	{
		$arConvertParams = [];
		if ($this->arParams['CONVERT_CURRENCY'] && Loader::includeModule('currency'))
		{
			$arCurrencyInfo = CCurrency::GetByID($this->arParams['CURRENCY_ID']);
			if ($arCurrencyInfo)
			{
				$arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
			}
		}

		foreach ($this->items as &$arItem)
		{
			$arItem['PRICES'] = \CIBlockPriceTools::GetItemPrices(
				$arItem['IBLOCK_ID'],
				$this->prices,
				$arItem,
				'N',
				$arConvertParams
			);
				
			$arItem['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromList($arItem['PRICES']);
		}

		unset($arItem);
	}

	/**
	 * @return array
	 */
	protected function getFormattingItems(): array
	{
		$arItems = [];

		$defaultMeasure = $this->getDefaultMeasure();

		foreach ($this->items as $arItem)
		{
			$arItemData = [
				'id' => $arItem['ID'],
				'name' => $arItem['NAME'],
				'code' => $arItem['CODE'],
				'article' => $arItem['PROPERTY_'.$this->arParams['PROP_CODE'].'_VALUE'],
				'detailPage' => $arItem['DETAIL_PAGE_URL'],
				'quantity' => 1 * $arItem['QUANTITY'],
			];

			if ($arItem['MIN_PRICE'])
			{
				$arItemData['price'] = $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];
				$arItemData['priceValue'] = $arItem['MIN_PRICE']['DISCOUNT_VALUE'];
				$arItemData['canBuy'] = $arItem['MIN_PRICE']['CAN_BUY'] == 'Y';
			}
			else
			{
				$arItemData['price'] = null;
				$arItemData['priceValue'] = null;
				$arItemData['canBuy'] = false;
			}
			
			if (!empty($arItem['MEASURE']) && isset($this->measures[$arItem['MEASURE']]))
			{
				$arItemData['measureName'] = $this->measures[$arItem['MEASURE']]['TITLE'];
			}
			else
			{
				$arItemData['measureName'] = $defaultMeasure['TITLE'];
			}

			$arItems[] = $arItemData;
		}

		return $arItems;
	}

	/**
	 * @param string $query
	 * 
	 * @return array
	 */
	public function searchAction(string $query = ''): array
	{
		$this->checkModules();

		$query = trim($query);
		if (strlen($query) < $this->arParams['MIN_STRLEN'])
		{
			return [];
		}

		$this->loadPrices();
		$this->loadItemsByQuery($query);
		$this->loadItemPrices();
		$this->loadMeasures();

		return $this->getFormattingItems();
	}

	/**
	 * @param int $productId
	 * 
	 * @return array
	 */
	public function addtobasketAction(int $productId): array
	{
		$this->checkModules();

		$arRatio = \Bitrix\Catalog\MeasureRatioTable::getCurrentRatio($productId);
		$quantity = isset($arRatio) ? $arRatio[$productId] : 1;

		$product = [
			'PRODUCT_ID' => $productId,
			'QUANTITY' => $quantity
		];

		$basketResult = \Bitrix\Catalog\Product\Basket::addProduct($product);

		return [
			'isSuccess' => $basketResult->isSuccess(),
			'errorMessage' => implode('; ', $basketResult->getErrorMessages()),
			'ratio' => $ratio
		];
	}

	/**
	 * @return void
	 */
	public function executeComponent(): void
	{
		try 
		{
			$this->setFrameMode(false);
			$this->checkModules();

			// $this->loadPrices();
			// $this->loadItemsByQuery('177');
			// $this->loadItemPrices();
			// $this->loadMeasures();

			$this->includeComponentTemplate();
		}
		catch(SystemException $e)
		{
			\ShowError($e->getMessage());
		}
	}
}
