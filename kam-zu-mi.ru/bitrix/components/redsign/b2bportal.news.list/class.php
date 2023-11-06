<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Web\Uri;
use \Bitrix\Main\Localization\Loc;
use \Redsign\B2BPortal\Traits;

defined('B_PROLOG_INCLUDED') || die();

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

class RSB2BPortalNewsListComponent extends \CBitrixComponent
{
	const ACTION_CHANGE_SORT = 'sorting';
	const ACTION_CHANGE_PERPAGE = 'perpage';

	use Traits\Sorting;
	use Traits\Paging;

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

		// if (!empty($this->globalFilter['SECTION_ID']))
		// {
		// 	$params['SECTION_ID'] = $this->globalFilter['SECTION_ID'];
		// }

		// if (empty($params['SECTION_ID']))
		// {
		// 	$params['ALLOW_WO_SECTION'] = 'Y';
		// 	$params['BY_LINK'] = 'Y';
		// }

		// echo"<textarea style='width:100%;height:250px;color:red;'>";
		// print_r($params);
		// echo"</textarea>";

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
