<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Web\Uri;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Engine\Contract\Controllerable;
use \Redsign\B2BPortal\Traits;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CIntranetToolbar $INTRANET_TOOLBAR
 */

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('redsign.b2bportal'))
	return;

if (!\Bitrix\Main\Loader::includeModule('iblock'))
{
	ShowError('IBLOCK_MODULE_NOT_INSTALLED');
	return;
}

\CBitrixComponent::includeComponentClass('bitrix:catalog.section');

class RSB2BPortalCatalogSectionComponent extends CatalogSectionComponent implements Controllerable
{
	const ACTION_CHANGE_SORT = 'sorting';
	const ACTION_CHANGE_PERPAGE = 'perpage';

	use Traits\CatalogSectionTable;
	use Traits\CatalogSectionActions;
	use Traits\Sorting;
	use Traits\Paging;

	public function configureActions(): array
	{
		return [
			'multipleadd2basket' => [
				'prefilters' => []
			]
		];
	}

	public function onPrepareComponentParams($params)
	{
		$params = parent::onPrepareComponentParams($params);

		$params['AJAX_URL'] = $params['SET_URL'] = $this->getRedirectUrl();

		$this->prepareSortingParams($params, self::ACTION_CHANGE_SORT);
		$this->preparePagingParams($params, self::ACTION_CHANGE_PERPAGE);

		$params['CATALOG_SORTER'] = [
			'ACTION_CHANGE_SORT' => self::ACTION_CHANGE_SORT,
			'ACTION_CHANGE_PERPAGE' => self::ACTION_CHANGE_PERPAGE,
			'PERPAGE_DROPDOWN' => $params['PERPAGE_FIELDS'],
		];

		if (!empty($this->globalFilter['SECTION_ID']))
		{
			$params['SECTION_ID'] = $this->globalFilter['SECTION_ID'];
		}

		if (empty($params['SECTION_ID']))
		{
			$params['ALLOW_WO_SECTION'] = 'Y';
			$params['BY_LINK'] = 'Y';
		}

		if (!isset($params['PRODUCT_DISPLAY_MODE']))
		{
			$params['PRODUCT_DISPLAY_MODE'] = 'Y';
		}

		if (!isset($params['SHOW_MAX_QUANTITY']))
		{
			$params['SHOW_MAX_QUANTITY'] = 'Y';
		}
		
		if (!isset($params['RELATIVE_QUANTITY_FACTOR']) || !is_numeric($params['RELATIVE_QUANTITY_FACTOR']))
		{
			$params['RELATIVE_QUANTITY_FACTOR'] = 100;
		}

		// $params['FILL_ITEM_ALL_PRICES'] = (is_array($params['PRICE_CODE']) && count($params['PRICE_CODE']) > 1) ? 'Y' :  $params['FILL_ITEM_ALL_PRICES'];
		$params['FILL_ITEM_ALL_PRICES'] = 'Y';

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
}
