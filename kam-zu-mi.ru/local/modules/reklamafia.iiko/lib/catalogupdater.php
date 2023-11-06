<?php

namespace Reklamafia\Iiko;

use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\SectionTable;

use Reklamafia\Iiko\IikoApi;
use Reklamafia\Iiko\IikoOrganization;
use Reklamafia\Iiko\IikoProduct;


class CatalogUpdater
{
	const MODULE_ID = "reklamafia.iiko";
	const IBLOCK_ID = 25;

	function isElementExists($groups, $key, $value)
	{
		foreach ($groups as $group) {
			//Debug::dump($group->id);
			if ($group->$key == $value) {
				return true;
			}
		}

		return false;
	}

	public static function update()
	{
		Loader::includeModule("iblock");
		Loader::includeModule('catalog');


		// Получаем данные в API
		$iiko = new IikoApi();
		$orgList = $iiko->getOrganizationList();
		$organization = new IikoOrganization($orgList[0]);
		$iiko->setOrganization($organization);

		$groups = $iiko->getGroups();
		$menu = $iiko->getNomenclature();

		$arTranslitParams = array(
			"max_len" => "100",
			"change_case" => "L",
			"replace_space" => "-",
			"replace_other" => "-",
			"delete_repeat_replace" => "true",
			"use_google" => "false",
		);

		// ========== РАЗДЕЛЫ ===========

		// Удаляем разделы, который нет в выгрузке iiko
		$sections = SectionTable::getList(array(
			'filter' => array(
				'IBLOCK_ID' => self::IBLOCK_ID,
				'DEPTH_LEVEL' => 1,
			),
			'select' =>  array('ID', 'CODE', 'NAME', 'XML_ID'),
		));

		while ($section = $sections->fetchObject()) {
			if (!self::isElementExists($groups, "id", $section->getXmlID())) {
				SectionTable::Delete($section->getID());
			}
		}


		// Создаём (обновляем) разделы
		$arFields = array();
		$groupIdToSectionId = array();
		$CIBlockSection = new \CIBlockSection;

		foreach ($groups as $group) {
			if (!$group->isIncludedInMenu) {
				continue;
			}

			$sections = SectionTable::getList(array(
				'filter' => array(
					'IBLOCK_ID' => self::IBLOCK_ID,
					'XML_ID' => $group->id,
					'DEPTH_LEVEL' => 1,
				),
				'select' =>  array('ID', 'PICTURE'),
			))->fetchAll();

			// Если элемент найден, то обновим его. Иначе - создадим новый.
			if (count($sections) > 0) {
				$section = $sections[0];

				$arFields = array(
					"NAME" => $group->name,
					"SORT" => $group->order,
					"DESCRIPTION" => $group->description,
				);

				if (is_null($section['PICTURE']) && count($group->images) > 0) {
					$img = \CFile::MakeFileArray($group->images[0]->imageUrl);
					$arFields['PICTURE'] = $img;
				};

				$CIBlockSection->Update($section['ID'], $arFields);
				$groupIdToSectionId[$group->id] = $section['ID'];
			} else {

				$arFields = array(
					"CODE"    => \Cutil::translit($group->name, "ru", $arTranslitParams),
					"ACTIVE" => "Y",
					"IBLOCK_SECTION_ID" => "",
					"IBLOCK_ID" => self::IBLOCK_ID,
					"NAME" => $group->name,
					"SORT" => $group->order,
					"EXTERNAL_ID" => $group->id,
					"DESCRIPTION" => $group->description,
				);

				if (count($group->images) > 0) {
					$img = \CFile::MakeFileArray($group->images[0]->imageUrl);
					$arFields['PICTURE'] = $img;
				};

				$sId = $CIBlockSection->Add($arFields);
				$groupIdToSectionId[$group->id] = $sId;
			}
		}


		// ========== БЛЮДА ===========

		// Удаляем блюда, который нет в выгрузке iiko
		$products = ElementTable::getList(array(
			'filter' => array(
				'IBLOCK_ID' => self::IBLOCK_ID,
			),
			'select' =>  array('ID', 'XML_ID'),
		));

		while ($product = $products->fetchObject()) {
			if (!self::isElementExists($groups, "id", $product->getXmlID())) {
				ElementTable::Delete($product->getID());				
				//Debug::dump('Будет удалён', $product->getName());
			}
		}


		// Добавляем\обновлем блюда
		$CIBlockElement = new \CIBlockElement;

		foreach ($menu as $product) {
			if ($product->type !== 'dish' || $product->isIncludedInMenu !== true)
				continue;

			$oldProducts = ElementTable::getList(array(
				'filter' => array(
					'IBLOCK_ID' => self::IBLOCK_ID,
					'XML_ID' => $product->id,
				),
				'select' =>  array('ID', 'NAME', 'DETAIL_PICTURE'),
			))->fetchAll();

			// Если продукты найдены, то обновляем, иначе - добавляем новый продукт
			if (count($oldProducts) > 0) {
				$oldProduct = $oldProducts[0];				

				/*
				$PROP = array(
					"PROPERTY_WEIGHT" => $product->weight,
				);*/
				$arLoadProductArray = array(
					"NAME" => $product->name,
					"ACTIVE" => "Y",
					"PREVIEW_TEXT" => $product->description,
					"IBLOCK_SECTION_ID" => $groupIdToSectionId[$product->parentGroup],
					//'PROPERTY_VALUES' => $PROP,
				);

				if (is_null($oldProduct['DETAIL_PICTURE']) && count($product->images) > 0) {
					$img = \CFile::MakeFileArray($product->images[0]->imageUrl);
					$arLoadProductArray['DETAIL_PICTURE'] = $img;
				};

				$IBlockElementID = $CIBlockElement->Update($oldProduct['ID'], $arLoadProductArray);
				$IBlockElementID = $CIBlockElement->SetPropertyValues($oldProduct['ID'], self::IBLOCK_ID, ($product->weight*1000)." гр.", "PROPERTY_WEIGHT");

				// Удаляем старые цены
				$allProductPrices = \Bitrix\Catalog\PriceTable::getList([
					"select" => ["*"],
					"filter" => [
						"=PRODUCT_ID" => $oldProduct['ID'],
					],
					"order" => ["CATALOG_GROUP_ID" => "ASC"]
				])->fetchAll();

				foreach ($allProductPrices as $productPrice) {
					\Bitrix\Catalog\Model\Price::delete($productPrice['ID']);
				}
				//Debug::dump($allProductPrices);

				// Устанавливаем новую цену
				$newPrice = [
					'PRODUCT_ID' => $oldProduct['ID'],
					'CATALOG_GROUP_ID' => 46,
					'PRICE' => $product->price,
					'CURRENCY' => 'RUB',
				];
				\Bitrix\Catalog\Model\Price::add($newPrice);
			} else {

				// --
				$PROP = array(
					"PROPERTY_WEIGHT" => ($product->weight * 1000)." гр.",
				);
				$arLoadProductArray = array(
					"CODE" => \Cutil::translit($product->name, "ru", $arTranslitParams),
					"IBLOCK_SECTION_ID" => $groupIdToSectionId[$product->parentGroup],
					"IBLOCK_ID" => self::IBLOCK_ID,
					"EXTERNAL_ID" => $product->id,
					"NAME" => $product->name,
					"ACTIVE" => "Y",
					"PREVIEW_TEXT" => $product->description,
					'PROPERTY_VALUES' => $PROP,
				);

				if (count($product->images) > 0) {
					$img = \CFile::MakeFileArray($product->images[0]->imageUrl);
					$arLoadProductArray['DETAIL_PICTURE'] = $img;
				};

				$IBlockElementID = $CIBlockElement->Add($arLoadProductArray);

				// ---
				if ($IBlockElementID > 0) {
					$productFields = array(
						"ID" => $IBlockElementID,
						"VAT_ID" => 1,
						"VAT_INCLUDED" => "Y",
						"AVAILABLE" => "Y",
						"CAN_BUY_ZERO" => "Y",
						"QUANTITY" => 100,
						"TYPE " => \Bitrix\Catalog\ProductTable::TYPE_PRODUCT
					);

					$productResult = \Bitrix\Catalog\Model\Product::add($productFields);
					if ($productResult) {
						$update = [
							'PRODUCT_ID' => $IBlockElementID,
							'CATALOG_GROUP_ID' => 46,
							'PRICE' => $product->price,
							'CURRENCY' => 'RUB',
						];
						\Bitrix\Catalog\Model\Price::add($update);
					}
				}
			}
		}


		return 'Reklamafia\Iiko\CatalogUpdater::update();';
	}
}
