<?php

use Bitrix\ 
{
	Main\Context,
	Main\Loader,
	Main\SystemException,
	Main\Localization\Loc,
	Main\ORM,
	Main\Web,
	Main\Engine\ActionFilter,
	Main\Engine\Contract\Controllerable
};

use Redsign\B2BPortal\DI;

Loc::loadMessages(__FILE__);

class RedsignB2BPortalBasketImports extends CBitrixComponent  implements Controllerable
{
	protected const DEFAULT_PROP_CODE = 'CML2_ARTICLE';
	
	protected $reader;
	protected $request;

	/**
	 * @return array
	 */
	public function configureActions(): array
	{
		return [
			'check' => [
				'prefilters' => [
					new ActionFilter\Csrf(),
				]
			],
			'textImport' => [
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
			'OFFERS_PROP_CODE'
		];
	}

	public function __construct($component = null)
	{
		parent::__construct($component);

		$this->checkModules();

		$container = DI\ServiceContainer::getInstance();

		$this->reader = $container->get('Spreadsheet\Reader');
		$this->request = $container->get('Request');
		$this->httpClient = $container->get('HttpClient');
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

	/**
	 * @param array $arParams
	 * 
	 * @return array
	 */
	public function onPrepareComponentParams(array $arParams): array
	{
		if (empty($arParams['PROP_CODE']))
		{
			$arParams['PROP_CODE'] = self::DEFAULT_PROP_CODE;
		}

		if (empty($arParams['OFFERS_PROP_CODE']))
		{
			$arParams['OFFERS_PROP_CODE'] = self::DEFAULT_PROP_CODE;
		}

		return $arParams;
	}

	public function checkAction(array $codes): array
	{

		if (count($codes) == 0)
		{
			return [];
		}

		$arFoundItems = [];
		$products = $this->getProductsByCodes($codes);

		foreach($products as $arItem)
		{
			$arFoundItems[] = $arItem['CODE'];
		}

		return $arFoundItems;
	}

	public function getMeasureRatio($productId)
	{
		$ratios = \Bitrix\Catalog\MeasureRatioTable::getCurrentRatio($productId);
		return $ratios[$productId];
	}

	/**
	 * @param int $productId
	 * 
	 * @return array
	 */
	public function addtobasket($productId, $quantity = null)
	{
		if (is_null($quantity))
		{
			$quantity = $this->getMeasureRatio($productId);
		}

		$product = [
			'PRODUCT_ID' => $productId,
			'QUANTITY' => $quantity
		];

		$basketResult = \Bitrix\Catalog\Product\Basket::addProduct($product);

		return $basketResult;
	}

	/**
	 * @param int $productId
	 * @param int|float $quantity
	 * 
	 * @return \Bitrix\Main\Result
	 */
	protected function addProductToBasket(int $productId, $quantity)
	{
		$productData = [
			'PRODUCT_ID' => $productId,
			'QUANTITY' => $quantity
		];

		return \Bitrix\Catalog\Product\Basket::addProduct($productData);
	}

	protected function getOffersIblockId() {
		
		if (Loader::includeModule('catalog'))
		{
			$iterator = \Bitrix\Catalog\CatalogIblockTable::getList(array(
				'select' => array('IBLOCK_ID'),
				'filter' => array('=PRODUCT_IBLOCK_ID' => $this->arParams['IBLOCK_ID'])
			));
	
			while ($row = $iterator->fetch())
			{
				return $row['IBLOCK_ID'];
			}
		}
		
		return false;
	}

	/**
	 * @param array $codes
	 * 
	 * @return array
	 */
	protected function getProductsByCodes(array $codes)
	{
		$products = [];

		$offersIblockId = $this->getOffersIblockId();
		if ($offersIblockId)
		{
			$arFilter = ['LOGIC' => 'OR'];
			$arFilter[] = [
				'=IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
				'=PROPERTY_'.$this->arParams['PROP_CODE'] => $codes,
				'=CATALOG_TYPE' => \Bitrix\Catalog\ProductTable::TYPE_PRODUCT
			];
			$arFilter[] = [
				'=IBLOCK_ID' => $offersIblockId,
				'=PROPERTY_'.$this->arParams['PROP_CODE'] => $codes,
				'=CATALOG_TYPE' => \Bitrix\Catalog\ProductTable::TYPE_OFFER
			];
		}
		else
		{
			$arFilter = [
				'=IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
				'=PROPERTY_'.$this->arParams['PROP_CODE'] => $codes
			];
		}

		$productIterator = CIBlockElement::GetList(
			['SORT' => 'ASC'],
			$arFilter,
			false,
			false,
			['ID', 'NAME', 'MEASURE', 'PROPERTY_'.$this->arParams['PROP_CODE']]
		);

		while ($product = $productIterator->GetNext())
		{
			$id = (int) $product['ID'];

			$products[$id] = [
				'ID' => $id,
				'NAME' => $product['NAME'],
				'MEASURE' => $product['MEASURE'],
				'CODE' =>  $product['PROPERTY_'.$this->arParams['PROP_CODE'].'_VALUE']
			];
		}

		return $products;
	}

	/**
	 * returns an array of measures as id => name
	 * 
	 * @return array
	 */
	protected function getMeasureNames()
	{
		$names = [];

		$iterator = \CCatalogMeasure::getList(
			['IS_DEFAULT' => 'DESC'],
			[],
			false,
			false,
			['ID', 'SYMBOL_RUS']
		);

		while ($measure = $iterator->fetch())
		{
			$names[$measure['ID']] = $measure['SYMBOL_RUS'];
		}

		return $names;
	}

	/**
	 * returns an array of ratios as productId => ratio
	 * 
	 * @param $productIds
	 * 
	 * @return array
	 */
	protected function getProductsRatios(array $productIds)
	{
		if (empty($productIds))
		{
			return [];
		}

		$ratios = array_fill_keys($productIds, 1);

		$iterator = \Bitrix\Catalog\MeasureRatioTable::getList([
			'select' => ['ID', 'RATIO', 'IS_DEFAULT', 'PRODUCT_ID'],
			'filter' => ['@PRODUCT_ID' => $productIds],
			'order' => ['PRODUCT_ID' => 'ASC']
		]);

		while ($row = $iterator->fetch())
		{
			$ratio = ((float)$row['RATIO'] > (int)$row['RATIO'] ? (float)$row['RATIO'] : (int)$row['RATIO']);
			if ($ratio > CATALOG_VALUE_EPSILON)
			{
				$id = (int) $row['PRODUCT_ID'];
				$ratios[$id][$row['ID']] = $row;
			}
		}

		return $ratios;
	}

	/**
	 * checks if the specified file type is valid
	 * 
	 * @param $fileExtension
	 * @throws \Exception
	 * @return boolean
	 */
	protected function checkExtensions($fileExtension)
	{
		if (!in_array($fileExtension, ['csv', 'xlsx', 'ods']))
		{
			throw new \Exception('Invalid file format. Supported formats: csv, xlsx, ods.');

			return false;
		}

		return true;
	}

	/**
	 * Download the file from the request and returns the path to it or false.
	 * 
	 * @return string
	 * @return string|boolean
	 */
	protected function loadFileFromRequest()
	{
		$uploadedFile = $this->request->getFile('file');
		
		$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/upload/';
		$fileName = $uploadedFile['name'];
		$filePath = $uploadDir.$fileName;
		$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
		
		$this->checkExtensions($fileExtension);

		if (is_uploaded_file($uploadedFile['tmp_name']))
		{
			$r = move_uploaded_file($uploadedFile['tmp_name'], $filePath);

			if ($r)
			{
				return $filePath;
			}
		}

		return false;
	}

	/**
	 * Download file by reference and returns the path to it or false/
	 * 
	 * @param string $path
	 * @return string|boolean
	 */
	protected function loadFileByPath(string $path)
	{
		$uploadDir = $_SERVER['DOCUMENT_ROOT'].'/upload/';
		
		if (preg_match('`^https?://docs.google.com/spreadsheets/.*output=(csv|xlsx|ods)$`', $path, $matches))
		{
			$fileExtension = $matches[1];
			$fileName = randString(11).'.'.$fileExtension;
			$filePath = $uploadDir.$fileName;
		}
		else
		{
			$fileName = pathinfo($path, PATHINFO_BASENAME);
			$filePath = $uploadDir.$fileName;
			$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
		}


		$this->checkExtensions($fileExtension);

		$r = $this->httpClient->download($path, $filePath);

		if ($r)
		{
			return $filePath;
		}

		return false;
	}

	/**
	 * returns an array of codes with the status of add to basket or false if data is empty
	 * 
	 * @param array $data
	 * 
	 * @return array|boolean
	 */
	public function addtobasketAction(array $data)
	{

		if (count($data) === 0)
		{
			return false;
		}
		
		$productCodes = array_keys($data);
		$products = $this->getProductsByCodes($productCodes);

		$result = array_fill_keys($productCodes, [
			'isSuccess' => false,
			'message' => Loc::getMessage('RS_BP_BI_PRODUCT_NOT_FOUND', [
				'#CODE#' => $code
			])
		]);

		if (count($products) > 0)
		{
			$measureNames = $this->getMeasureNames();
			$defaultMeasureName = current($measureNames);
			$ratios = $this->getProductsRatios(array_keys($products));

			foreach ($products as $product)
			{
				$id = $product['ID'];
				$code = $product['CODE'];
				$measure = $product['MEASURE'];
				$ratio = isset($ratios[$id]) ? $ratios[$id] : 1;
				$quantity = isset($data[$code]) && $data[$code] > 0 ? $data[$code] : 1;
	
				$basketResult = $this->addProductToBasket($id, $ratio * $quantity);
				if ($basketResult->isSuccess())
				{
					$result[$code] = [
						'isSuccess' => true,
						'message' => Loc::getMessage('RS_BP_BI_ADDTOBASKET_SUCCESS', [
							'#CODE#' => $code,
							'#NAME#' => $product['NAME'],
							'#QUANTITY#' => $quantity,
							'#MEASURE_NAME#' => isset($measureNames[$measure]) ? $measureNames[$measure] : $defaultMeasureName
						])
					];
				}
				else
				{
					$result[$code] = [
						'isSuccess' => false,
						'message' => Loc::getMessage('RS_BP_BI_ADDTOBASKET_ERROR', [
							'#CODE#' => $code,
							'#ERROR#' => implode('; ', $basketResult->getErrorMessages())
						])
					];
				}
			}
		}

		return $result;
	}

	/**
	 * action on file parsing. Returns an array of rows.
	 * 
	 * @param string $path
	 * @return void
	 */
	public function parseFileAction(string $path = null)
	{
		$filePath = is_null($path) ? $this->loadFileFromRequest() : $this->loadFileByPath($path);

		if (file_exists($filePath))
		{
			$this->reader->readFile($filePath);

			$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
			if ($fileExtension != 'csv')
			{
				$this->reader->setInputEncoding('UTF-8');
			}
	
			$activeSheetIndex = $this->reader->getActiveSheetIndex();
			$rows = $this->reader->getSheetRows($activeSheetIndex);

			$this->reader->close();

			unlink($filePath);
			
			return $rows;
		}


		return [];
	}

	/**
	 * @return void
	 */
	public function executeComponent(): void
	{
		try 
		{
			$this->setFrameMode(false);

			$this->includeComponentTemplate();
		}
		catch(SystemException $e)
		{
			\ShowError($e->getMessage());
		}
	}
}