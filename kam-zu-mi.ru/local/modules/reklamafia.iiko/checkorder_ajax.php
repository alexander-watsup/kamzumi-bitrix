<?php
namespace Reklamafia\Iiko;

header("Content-Type: application/json");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Reklamafia\Iiko\IikoApi;

const MODULE_ID = "reklamafia.iiko";

$requestCity = isset($_GET['city'])?$_GET['city']: "";
$requestStreet = isset($_GET['street'])?$_GET['street']: "";

Loader::includeModule(MODULE_ID);

$iikoUser = Option::get(MODULE_ID, "iiko_user");
$iikoPassword = Option::get(MODULE_ID, "iiko_password");

$iiko = new IikoApi([
	'login' => $iikoUser,
	'password' => $iikoPassword
]);


$orgList = $iiko->getOrganizationList();
$organization = new IikoOrganization($orgList[0]);
$iiko->setOrganization($organization);

$product = $iiko->createProduct();
$product->name = $_GET['street'];

$customer = $iiko->createCustomer();
$customer->name = $_GET['name'];
$customer->phone = $_GET['phone'];

$address = Array(
	'city' => $requestCity,
	'street' => $requestStreet,
	'home' => $_GET['home'],
	'housing' => $_GET['housing'],
	'apartment' => $_GET['apartment']
);

$order = $iiko->createOrder();
$order->phone = $_GET['phone'];
$order->setProduct($product);
$order->setCustomer($customer);
$order->setAddress($address);


$res = $iiko->checkOrder($order);

echo (json_encode($res, JSON_UNESCAPED_UNICODE));


