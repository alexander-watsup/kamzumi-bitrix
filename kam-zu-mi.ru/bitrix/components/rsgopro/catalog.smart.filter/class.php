<?
use Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CBitrixComponent::includeComponentClass("bitrix:catalog.smart.filter");
class CRSMMCatalogSmartFilter extends CBitrixCatalogSmartFilter {}
