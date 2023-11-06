<?php

namespace Reklamafia\Iiko;

use Bitrix\Main\Loader;
use \Bitrix\Main\EventResult;
use Bitrix\Main\Config\Option;
use Reklamafia\Iiko\IikoApi;
use Reklamafia\Iiko\IikoOrganization;
use Reklamafia\Iiko\IikoProduct;
use Reklamafia\Iiko\IikoOrder;

class Order
{
    const MODULE_ID = "reklamafia.iiko";
    const DELIVERY_SERVICE_ID = "17"; // ИД службы доставки

    public static function OnSaleComponentOrderResultPreparedHandler($order, &$arUserResult, $request, &$arParams, &$arResult)
    {
        /*
        AddMessage2Log($request);
        AddMessage2Log($arParams);
        AddMessage2Log($arResult);*/

        //AddMessage2Log('1');

        return true;
    }


    public static function onSaleDeliveryServiceCalculateHandler(\Bitrix\Main\Event $event)
    {
        return;
/*
        $deliveryId = $event->getParameter('DELIVERY_ID');

        if ($deliveryId !== self::DELIVERY_SERVICE_ID) {
            return;
        }

        $baseResult = $event->getParameter('RESULT');
        $shipment = $event->getParameter('SHIPMENT');

        $propertyCollection = $shipment->getCollection()->getOrder()->getPropertyCollection();

        $name = $propertyCollection->getItemByOrderPropertyId(32)->getField('VALUE');
        $phone = $propertyCollection->getItemByOrderPropertyId(33)->getField('VALUE');
        $city = $propertyCollection->getItemByOrderPropertyId(35) ? $propertyCollection->getItemByOrderPropertyId(35)->getField('VALUE') : "";
        $street = $propertyCollection->getItemByOrderPropertyId(36) ? $propertyCollection->getItemByOrderPropertyId(36)->getField('VALUE') : "";
        $home = $propertyCollection->getItemByOrderPropertyId(37) ? $propertyCollection->getItemByOrderPropertyId(37)->getField('VALUE') : "";
        $housing = $propertyCollection->getItemByOrderPropertyId(38) ? $propertyCollection->getItemByOrderPropertyId(38)->getField('VALUE') : "";
        $apartment = $propertyCollection->getItemByOrderPropertyId(39) ? $propertyCollection->getItemByOrderPropertyId(39)->getField('VALUE') : "";

        

        $iiko = new IikoApi();
        $orgList = $iiko->getOrganizationList();
        $organization = new IikoOrganization($orgList[0]);
        $iiko->setOrganization($organization);


        $products = IikoApi::getIikoBasket($shipment->getCollection()->getOrder()->getBasket());

        $customer = $iiko->createCustomer();
        $customer->name = $name;
        $customer->phone = $phone;

        $address = array(
            'city' => $city,
            'street' => $street,
            'home' => $home,
            'housing' => $housing,
            'apartment' => $apartment
        );

        $order = $iiko->createOrder();
        $order->phone = $_GET['phone'];        
        $order->products = $products;
        $order->setCustomer($customer);
        $order->setAddress($address);

        $iikoPriceResult = $iiko->getDeliveryPrice($order);
		//AddMessage2Log($iikoPriceResult);

        $price = $baseResult->getDeliveryPrice();

        if ($iikoPriceResult['code'] == 0) {
            $price =  $iikoPriceResult['price'];
        }

        $baseResult->setDeliveryPrice($price);
        //$baseResult->setDescription("Описание");

                

        $event->addResult(
            new EventResult(
                EventResult::SUCCESS,
                array('RESULT' => $baseResult)
            )
        );
        
        return true;
        //return true;
        */
    }


    
}
