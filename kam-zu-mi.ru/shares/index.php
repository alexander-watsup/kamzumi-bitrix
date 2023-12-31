<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("пїЅпїЅпїЅпїЅпїЅ");

?>
<?php $APPLICATION->IncludeComponent(
	"bitrix:news", 
	"shares.1", 
	array(
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "28",
		"NEWS_COUNT" => "20",
		"USE_SEARCH" => "N",
		"SETTINGS_USE" => "Y",
		"USE_RSS" => "N",
		"USE_RATING" => "N",
		"USE_CATEGORIES" => "N",
		"USE_REVIEW" => "N",
		"USE_FILTER" => "N",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"CHECK_DATES" => "Y",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/shares/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"SET_LAST_MODIFIED" => "N",
		"SET_TITLE" => "Y",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"USE_PERMISSIONS" => "N",
		"STRICT_SECTION_CHECK" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"LIST_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "DURATION",
			1 => "SALE",
			2 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"LIST_TEMPLATE" => "list.1",
		"LIST_LAZYLOAD_USE" => "N",
		"LIST_DATE_SHOW" => "Y",
		"LIST_DATE_TYPE" => "DATE_ACTIVE_FROM",
		"LIST_DATE_FORMAT" => "d.m.Y",
		"LIST_DESCRIPTION_SHOW" => "Y",
		"DISPLAY_NAME" => "Y",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DETAIL_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "DURATION",
			1 => "SALE",
			2 => "",
		),
		"DETAIL_TEMPLATE" => "default.1",
		"DETAIL_BANNER_WIDE" => "Y",
		"DETAIL_BANNER_HEIGHT" => "500px",
		"DETAIL_DESCRIPTION_PROPERTY_DURATION" => "DURATION",
		"DETAIL_PROMO_PROPERTY_ELEMENTS" => "PROMO_ELEMENTS",
		"DETAIL_PROMO_IBLOCK_TYPE" => "content",
		"DETAIL_PROMO_IBLOCK_ID" => "29",
		"DETAIL_CONDITIONS_PROPERTY_ELEMENTS" => "CONDITIONS_ELEMENTS",
		"DETAIL_CONDITIONS_HEADER" => "Условия акции",
		"DETAIL_CONDITIONS_HEADER_POSITION" => "center",
		"DETAIL_CONDITIONS_IBLOCK_TYPE" => "content",
		"DETAIL_CONDITIONS_IBLOCK_ID" => "30",
		"DETAIL_CONDITIONS_COLUMNS" => "3",
		"DETAIL_FORM_SHOW" => "N",
		"DETAIL_VIDEOS_PROPERTY_ELEMENTS" => "VIDEOS_ELEMENTS",
		"DETAIL_VIDEOS_HEADER" => "Видео",
		"DETAIL_VIDEOS_HEADER_POSITION" => "center",
		"DETAIL_VIDEOS_IBLOCK_TYPE" => "content",
		"DETAIL_VIDEOS_IBLOCK_ID" => "35",
		"DETAIL_VIDEOS_COLUMNS" => "3",
		"DETAIL_VIDEOS_PROPERTY_URL" => "LINK",
		"DETAIL_GALLERY_PROPERTY_ELEMENTS" => "PHOTO_ELEMENTS",
		"DETAIL_GALLERY_HEADER" => "Галерея",
		"DETAIL_GALLERY_HEADER_POSITION" => "center",
		"DETAIL_GALLERY_IBLOCK_TYPE" => "content",
		"DETAIL_GALLERY_IBLOCK_ID" => "36",
		"DETAIL_GALLERY_LINE_COUNT" => "4",
		"DETAIL_GALLERY_WIDE" => "N",
		"DETAIL_SECTIONS_PROPERTY_ELEMENTS" => "CATALOG_SECTIONS",
		"DETAIL_SECTIONS_HEADER" => "Разделы",
		"DETAIL_SECTIONS_HEADER_POSITION" => "center",
		"DETAIL_SECTIONS_IBLOCK_TYPE" => "catalogs",
		"DETAIL_SECTIONS_IBLOCK_ID" => "25",
		"DETAIL_SECTIONS_PROPERTY_SECTIONS" => "CATALOG_SECTIONS",
		"DETAIL_SECTIONS_LINE_COUNT" => "5",
		"DETAIL_SERVICES_PROPERTY_ELEMENTS" => "SERVICES_ELEMENTS",
		"DETAIL_SERVICES_SETTINGS_USE" => "N",
		"DETAIL_SERVICES_LAZYLOAD_USE" => "N",
		"DETAIL_SERVICES_HEADER" => "Услуги",
		"DETAIL_SERVICES_HEADER_POSITION" => "center",
		"DETAIL_SERVICES_IBLOCK_TYPE" => "#CATALOGS_SERVICES_IBLOCK_TYPE#",
		"DETAIL_SERVICES_IBLOCK_ID" => "#CATALOGS_SERVICES_IBLOCK_ID#",
		"DETAIL_SERVICES_COLUMNS" => "2",
		"DETAIL_SERVICES_LINK_USE" => "Y",
		"DETAIL_SERVICES_INDENT_IMAGE_USE" => "N",
		"DETAIL_SERVICES_DESCRIPTION_USE" => "Y",
		"DETAIL_SERVICES_FOOTER_SHOW" => "N",
		"DETAIL_PRODUCTS_PROPERTY_ELEMENTS" => "CATALOG_ELEMENTS",
		"DETAIL_PRODUCTS_HEADER" => "Блюда",
		"DETAIL_PRODUCTS_HEADER_POSITION" => "center",
		"DETAIL_PRODUCTS_IBLOCK_TYPE" => "catalogs",
		"DETAIL_PRODUCTS_IBLOCK_ID" => "25",
		"DETAIL_PRODUCTS_OFFERS_LIMIT" => "0",
		"DETAIL_PRODUCTS_PRICE_CODE" => array(
			0 => "Kam-Zu-Mi",
		),
		"DETAIL_PRODUCTS_BASKET_URL" => "/personal/basket/",
		"DETAIL_PRODUCTS_HIDE_NOT_AVAILABLE_OFFERS" => "Y",
		"DETAIL_PRODUCTS_ACTION" => "buy",
		"DETAIL_PRODUCTS_BORDERS" => "Y",
		"DETAIL_PRODUCTS_COLUMNS" => "4",
		"DETAIL_PRODUCTS_COUNTER_SHOW" => "Y",
		"DETAIL_PRODUCTS_OFFERS_USE" => "Y",
		"DETAIL_PRODUCTS_CONSENT_URL" => "/company/consent/",
		"DETAIL_PRODUCTS_LAZY_LOAD" => "N",
		"DETAIL_PRODUCTS_DELAY_USE" => "N",
		"DETAIL_PRODUCTS_VOTE_SHOW" => "N",
		"DETAIL_PRODUCTS_QUANTITY_SHOW" => "N",
		"DETAIL_PRODUCTS_QUICK_VIEW_USE" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_DETAIL" => "N",
		"DETAIL_PRODUCTS_USE_COMPARE" => "Y",
		"DETAIL_PRODUCTS_COMPARE_NAME" => "compare",
		"DETAIL_PRODUCTS_PROPERTY_ORDER_USE" => "ORDER_USE",
		"DETAIL_PRODUCTS_PROPERTY_MARKS_RECOMMEND" => "RECOMMEND",
		"DETAIL_PRODUCTS_PROPERTY_MARKS_NEW" => "NEW",
		"DETAIL_PRODUCTS_PROPERTY_MARKS_HIT" => "HIT",
		"DETAIL_PRODUCTS_VOTE_MODE" => "rating",
		"DETAIL_PRODUCTS_QUANTITY_MODE" => "number",
		"DETAIL_PRODUCTS_OFFERS_PROPERTY_CODE" => array(
			0 => "OFFERS_SIZE",
		),
		"DETAIL_PRODUCTS_QUICK_VIEW_TEMPLATE" => "1",
		"DETAIL_PRODUCTS_QUICK_VIEW_PROPERTY_CODE" => array(
			0 => "",
			1 => "PROPERTY_TYPE",
			2 => "PROPERTY_QUANTITY_OF_STRIPS",
			3 => "PROPERTY_POWER",
			4 => "PROPERTY_PROCREATOR",
			5 => "PROPERTY_SCOPE",
			6 => "PROPERTY_DISPLAY",
			7 => "PROPERTY_WEIGTH",
			8 => "PROPERTY_ENERGY_CONSUMPTION",
			9 => "PROPERTY_SETTINGS",
			10 => "PROPERTY_COMPOSITION",
			11 => "PROPERTY_LENGTH",
			12 => "PROPERTY_SEASON",
			13 => "PROPERTY_PATTERN",
			14 => "",
		),
		"DETAIL_PRODUCTS_QUICK_VIEW_PROPERTY_MARKS_HIT" => "HIT",
		"DETAIL_PRODUCTS_QUICK_VIEW_PROPERTY_MARKS_NEW" => "NEW",
		"DETAIL_PRODUCTS_QUICK_VIEW_PROPERTY_MARKS_RECOMMEND" => "RECOMMEND",
		"DETAIL_PRODUCTS_QUICK_VIEW_PROPERTY_PICTURES" => "PICTURES",
		"DETAIL_PRODUCTS_QUICK_VIEW_PROPERTY_TEXT" => "",
		"DETAIL_PRODUCTS_QUICK_VIEW_WEIGHT_SHOW" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_QUANTITY_SHOW" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_QUANTITY_MODE" => "number",
		"DETAIL_PRODUCTS_QUICK_VIEW_ACTION" => "buy",
		"DETAIL_PRODUCTS_QUICK_VIEW_COUNTER_SHOW" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_DESCRIPTION_SHOW" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_GALLERY_PANEL" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_GALLERY_PREVIEW" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_INFORMATION_PAYMENT" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_INFORMATION_SHIPMENT" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_MARKS_SHOW" => "Y",
		"DETAIL_PRODUCTS_QUICK_VIEW_DESCRIPTION_MODE" => "preview",
		"DETAIL_PRODUCTS_QUICK_VIEW_PAYMENT_URL" => "/help/buys/",
		"DETAIL_PRODUCTS_QUICK_VIEW_SHIPMENT_URL" => "/help/buys/",
		"DETAIL_PRODUCTS_FORM_ID" => "3",
		"DETAIL_PRODUCTS_FORM_PROPERTY_PRODUCT" => "form_text_7",
		"DETAIL_PRODUCTS_FORM_TEMPLATE" => ".default",
		"DETAIL_LINKS_BUTTON" => "Смотреть все акции",
		"DETAIL_LINKS_SOCIAL_SHOW" => "Y",
		"DETAIL_LINKS_HANDLERS" => array(
			0 => "facebook",
			1 => "vk",
		),
		"DETAIL_LINKS_SHORTEN_URL_LOGIN" => "",
		"DETAIL_LINKS_SHORTEN_URL_KEY" => "",
		"DETAIL_LINKS_TITLE" => "Поделиться акцией",
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
		"DETAIL_PAGER_TITLE" => "Категории",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_SHOW_ALL" => "Y",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Категории",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "Y",
		"SHOW_404" => "Y",
		"FILE_404" => "/404.php",
		"COMPONENT_TEMPLATE" => "shares.1",
		"LIST_IBLOCK_DESCRIPTION_SHOW" => "Y",
		"DETAIL_LAZYLOAD_USE" => "N",
		"DETAIL_PRODUCTS_OFFERS_SORT_FIELD" => "sort",
		"DETAIL_PRODUCTS_OFFERS_SORT_ORDER" => "asc",
		"DETAIL_PRODUCTS_COLUMNS_MOBILE" => "1",
		"DETAIL_PRODUCTS_IMAGE_ASPECT_RATIO" => "1:1",
		"DETAIL_PRODUCTS_RECALCULATION_PRICES_USE" => "N",
		"DETAIL_PRODUCTS_QUICK_VIEW_SLIDE_USE" => "N",
		"DETAIL_PRODUCTS_QUICK_VIEW_PROPERTY_ORDER_USE" => "",
		"DETAIL_PRODUCTS_QUICK_VIEW_OFFERS_PROPERTY_PICTURES" => "",
		"DETAIL_PRODUCTS_QUICK_VIEW_LAZYLOAD_USE" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "#ELEMENT_ID#/",
		)
	),
	false
); ?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php") ?>
