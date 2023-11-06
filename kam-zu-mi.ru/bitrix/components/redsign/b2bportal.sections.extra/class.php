<?php

use Bitrix\ 
{
	Main\Loader,
	Main\SystemException,
	Main\Localization\Loc,
	Main\ORM,
	Main\Engine\Contract\Controllerable,
	Main\Engine\ActionFilter,
	Iblock,
	Catalog
};

use Redsign\
{
	B2BPortal\Catalog\Entity\SectionExtraTable
};

Loc::loadMessages(__FILE__);

class RedsignB2BPortalSectionsExtra extends CBitrixComponent implements Controllerable
{
	const EXCEPTION_MODULE_NOT_INSTALLED = 1000;
	const EXCEPTION_MODULE_IBLOCK_NOT_INSTALLED = 1001;
	const EXCEPTION_MODULE_CATALOG_NOT_INSTALLED = 1002;

	const UPDATE_MAX_EXECUTION_SECONDS = 20;

	protected $prices;
	protected $rows;

	public function configureActions(): array
	{
		return [
			'save' => [
				'prefilters' => [
					new ActionFilter\Csrf(),
				]
			]
		];
	}

	protected function listKeysSignedParameters(): array
	{
		return [
			'IBLOCK_ID'
		];
	}
	

	protected function checkModules()
	{
		if (!Loader::includeModule('redsign.b2bportal'))
		{
			throw new SystemException(
				Loc::getMessage('RSB2BPORTAL_SE_MODULE_NOT_INSTALLED'), 
				self::EXCEPTION_MODULE_NOT_INSTALLED
			);
		}
		
		if (!Loader::includeModule('iblock'))
		{
			throw new SystemException(
				Loc::getMessage('RSB2BPORTAL_SE_IBLOCK_MODULE_NOT_INSTALLED'), 
				self::EXCEPTION_MODULE_IBLOCK_NOT_INSTALLED
			);
		}

		if (!Loader::includeModule('catalog') || !Loader::includeModule('currency'))
		{
			throw new SystemException(
				Loc::getMessage('RSB2BPORTAL_SE_CATALOG_MODULE_NOT_INSTALLED'), 
				self::EXCEPTION_MODULE_CATALOG_NOT_INSTALLED
			);
		}
	}

	private function obtainPrices(): Catalog\Eo_Group_Collection
	{
		$prices = Catalog\GroupTable::getList([
			'select' => ['ID', 'NAME', 'SORT', 'XML_ID', 'CURRENT_LANG.ID', 'CURRENT_LANG.NAME'],
			'filter' => ['BASE' => 'N']
		])
			->fetchCollection();

		return $prices ?? Catalog\GroupTable::createCollection();
	}

	private function obtainRows(): Iblock\EO_Section_Collection 
	{
		$arSelectFields = [
			'ID',
			'NAME', 
			'LEFT_MARGIN', 
			'RIGHT_MARGIN', 
			'DEPTH_LEVEL', 
			'EXTRA'
		];

		$rows = Iblock\SectionTable::query()
			->registerRuntimeField(
				(new ORM\Fields\Relations\OneToMany(
					'EXTRA',
					SectionExtraTable::class,
					'SECTION'
				))
			)
			->setSelect($arSelectFields)
			->addFilter('IBLOCK_ID', $this->arParams['IBLOCK_ID'])
			->addOrder('LEFT_MARGIN')
			->fetchCollection();

		return $rows ?? Iblock\SectionTable::createCollection();
	}

	protected function formatPrices(Catalog\Eo_Group_Collection $prices): array
	{
		$arPrices = [];

		foreach ($prices as $price)
		{
			$currentLang = $price->getCurrentLang();

			$arPrices[$price->getId()] = [
				'ID' => $price->getId(),
				'CODE' => $price->getName(),
				'SORT' => $price->getSort(),
				'XML_ID' => $price->getXmlId(),
				'NAME' => $currentLang->getName()
			];
		}

		return $arPrices;
	}

	protected function getTreeRows(array &$arItems, $level = 1, &$i = 0): array
	{
		$returnArray = array();

		if (!is_array($arItems)) 
		{
			return $returnArray;
		}

		for (
			$currentItemKey = 0, $countItems = count($arItems);
			$i < $countItems;
			++$i
		) {
			$arItem = $arItems[$i];

			if ($arItem['DEPTH_LEVEL'] == $level) 
			{
				$returnArray[$currentItemKey++] = $arItem;
			} 
			elseif ($arItem['DEPTH_LEVEL'] > $level) 
			{
				$returnArray[$currentItemKey - 1]['CHILDREN'] = $this->getTreeRows(
					$arItems,
					$level + 1,
					$i
				);
			} 
			elseif ($level > $arItem['DEPTH_LEVEL']) 
			{
				--$i;
				break;
			}
		}

		return $returnArray;
	}

	protected function formatRows(Iblock\EO_Section_Collection $rows): array
	{
		$arRows = [];

		foreach ($rows as $row)
		{
			$arValues = $row->collectValues();
			$arValues['EXTRA'] = [];

			foreach ($this->prices as $price)
			{
				$priceId = $price->getId();

				$arValues['EXTRA'][$priceId] = [
					'ID' => null,
					'PRICE_ID' => $priceId,
					'VALUE' => 0
				];
			}

			foreach ($row->get('EXTRA') as $extra)
			{
				$priceId = $extra->getPriceId();

				$arValues['EXTRA'][$priceId]['ID'] = $extra->getId();
				$arValues['EXTRA'][$priceId]['VALUE'] = $extra->getValue();
			}

			$arRows[] = $arValues;
		}

		return $arRows;
	}

	protected function getResult(): void
	{
		$this->prices = $this->obtainPrices();
		$this->rows = $this->obtainRows();

		$this->arResult['PRICES'] = $this->formatPrices($this->prices);
		$this->arResult['ROWS'] = $this->getTreeRows(
			$this->formatRows($this->rows)
		);
	}

	public function executeComponent(): void
	{
		try 
		{
			$this->setFrameMode(false);
			$this->checkModules();

			$this->getResult();

			$this->includeComponentTemplate();
		}
		catch(SystemException $e)
		{
			\ShowError($e->getMessage());
		}
	}

	public function saveAction($changes, $nLastItemId = 0): array
	{
		$this->checkModules(); 

		$arReturn = [
			'COMPLETED' => [],
			'UPDATED' => [],
			'ERRORS' => [],
			'LAST_ITEM_ID' => $nLastItemId,
		];

		$nStartTime = time();

		$baseGroup = Catalog\GroupTable::query() 
			->setSelect(['ID', 'NAME', 'SORT', 'XML_ID', 'BASE'])
			->where('BASE', 'Y')
			->fetchObject();

		$sBaseCurrency = \CCurrency::GetBaseCurrency();

		foreach ($changes as $change)
		{
			$nSectionId = (int) $change['SECTION_ID'];
			$nPriceId = (int) $change['PRICE_ID'];
			$nExtraId = $change['EXTRA_ID'] ?? null;
			$nExtraValue = $change['VALUE'];

			$extra = SectionExtraTable::createOrUpdate([
				'ID' => $nExtraId,
				'SECTION_ID' => $nSectionId,
				'PRICE_ID' => $nPriceId,
				'VALUE' => $nExtraValue
			]);

			$arReturn['UPDATED'][] = $extra->collectValues();

			$elements = Iblock\ElementTable::query()
				->setSelect(['ID', 'NAME', 'IBLOCK_SECTION_ID', 'PRICE', 'BASE_PRICE'])
				->registerRuntimeField(
					(new ORM\Fields\Relations\Reference(
						'PRICE',
						Catalog\PriceTable::class,
						ORM\Query\Join::on('this.ID', 'ref.PRODUCT_ID')
							->where('ref.CATALOG_GROUP_ID', $nPriceId)
					))
				)
				->registerRuntimeField(
					(new ORM\Fields\Relations\Reference(
						'BASE_PRICE',
						Catalog\PriceTable::class,
						ORM\Query\Join::on('this.ID', 'ref.PRODUCT_ID')
							->where('ref.CATALOG_GROUP_ID', $baseGroup->getId())
					))
				)
				->where('IBLOCK_SECTION_ID', $extra->getSectionId())
				->where('IBLOCK_ID', $this->arParams['IBLOCK_ID'])
				->where('ID', '>', $arReturn['LAST_ITEM_ID'])
				->addOrder('ID', 'ASC')
				->fetchCollection();

			foreach ($elements as $i => $element)
			{
				$basePrice = $element->get('BASE_PRICE');
				$price = $element->get('PRICE');
				
				if (!$basePrice)
				{
					$arReturn['ERRORS'][] = Loc::getMessage('RS_BP_SE_UPDATE_PRICES_ERROR', [
						'#CODE#' => 0,
						'#ELEMENT_NAME#' => $element->getName(),
						'#ERROR' => Loc::getMessage('RS_BP_SE_UPDATE_PRICES_ERROR_BASE_PRICE_NOT_FOUND')
					]);

					continue;
				}
				
				$nPriceValue = $basePrice->getPrice() + ($basePrice->getPrice() / 100 * $nExtraValue);
				if ($nPriceValue < 0)
				{
					$nPriceValue = 0;
				}

				if ($price)
				{
					$result = Catalog\Model\Price::update($price->getId(), [
						'PRODUCT_ID' => $element->getId(),
						'CATALOG_GROUP_ID' => $nPriceId,
						'PRICE' => $nPriceValue
					]);
				}
				else
				{
					$result = Catalog\Model\Price::add([
						'CURRENCY' => $sBaseCurrency,
						'PRODUCT_ID' => $element->getId(),
						'CATALOG_GROUP_ID' => $nPriceId,
						'PRICE' => $nPriceValue
					]);
				}

				if (!$result->isSuccess())
				{
					foreach ($result->getErrors() as $error)
					{
						$arReturn['ERRORS'][] = Loc::getMessage('RS_BP_SE_UPDATE_PRICES_ERROR', [
							'#CODE#' => $error->getCode(),
							'#ELEMENT_NAME#' => $element->getName(),
							'#ERROR' => $error->getMessage()
						]);
					}
				}

				$arReturn['LAST_ITEM_ID'] = $element->getId();

				if (time() - $nStartTime >= self::UPDATE_MAX_EXECUTION_SECONDS)
				{
					break 2;
				}
			}

			$arReturn['COMPLETED'][] = $extra->getId();
			$arReturn['LAST_ITEM_ID'] = 0;
		}

		return $arReturn;
	}
}