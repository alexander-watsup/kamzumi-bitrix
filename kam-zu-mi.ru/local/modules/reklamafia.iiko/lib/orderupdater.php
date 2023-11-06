<?php

namespace Reklamafia\Iiko;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Event;
use Bitrix\Main\Diag\Debug;
use Reklamafia\Iiko\IikoApi;
use Reklamafia\Iiko\IikoOrganization;

require_once dirname(__FILE__) . '/../vendor/PackageLoader.php';

class OrderUpdater
{

  const MODULE_ID = "reklamafia.iiko";
  const TELEGRAM_BOT_ID = '1753663316:AAH5WzAtTM8BfG_7_fh6E8C-hQMUfHBlWJk';
  const TELEGRAM_CHAT_ID = '-526133363';

  protected static $paymentHandlerDisallow = false;

  public static function OnOrderAddHandler(Event $event)
  {
    if (self::$paymentHandlerDisallow)
      return;

    $loader = new \PackageLoader\PackageLoader();
    $loader->load(dirname(__FILE__) . '/../vendor/telegram');

    $onlinePaymentSystemId = 14; // Способ оплаты - Сбербанк на сайте
    $siteId = "s2";


    $order = $event->getParameter("ENTITY");
    $oldValues = $event->getParameter("VALUES");
    $isNew = $event->getParameter("IS_NEW");

    if ($order->getSiteId() !== $siteId) {
      return true;
    }
    if($order->getUserId()===1) {
      return true;
    }

    $deliveryIdList = $order->getDeliveryIdList();
    $deliveryId = $deliveryIdList[0];
    $paysystemIdList = $order->getPaySystemIdList();
    $paysystemId = $paysystemIdList[0];

    // Новый заказ. Если не онлайн-оплата, то сразу отправим заказ в iiko
    if ($isNew && $paysystemId !== $onlinePaymentSystemId) {


      if ($deliveryId == 16) { // Самовывоз
        $propertyCollection = $order->getPropertyCollection();

        $name = $propertyCollection->getItemByOrderPropertyId(32) ? $propertyCollection->getItemByOrderPropertyId(32)->getField('VALUE') : "";
        $phone = $propertyCollection->getItemByOrderPropertyId(33) ? $propertyCollection->getItemByOrderPropertyId(33)->getField('VALUE') : "";
        $personsCount = $propertyCollection->getItemByOrderPropertyId(41) ? $propertyCollection->getItemByOrderPropertyId(41)->getField('VALUE') : "";
        $comment = $order->getField('USER_DESCRIPTION');

        $iiko = new IikoApi();
        $orgList = $iiko->getOrganizationList();
        $organization = new IikoOrganization($orgList[0]);
        $iiko->setOrganization($organization);

        $products = IikoApi::getIikoBasket($order->getBasket());

        $customer = $iiko->createCustomer();
        $customer->name = $name;
        $customer->phone = $phone;

        $address = array(
          'city' => "Калининград",
          'street' => "Аксакова",
          'home' => "100",
          'housing' => "",
          'apartment' => "1"
        );

        $iikoOrder = $iiko->createOrder();
        $iikoOrder->phone = $phone;
        $iikoOrder->isSelfService = true;
        $iikoOrder->personsCount = $personsCount;
        $iikoOrder->comment = $comment;
        $iikoOrder->products = $products;
        $iikoOrder->setCustomer($customer);
        $iikoOrder->setAddress($address);

        $res = $iiko->sendOrder($iikoOrder);

        if ($res->orderId) {
          $propertyCollection->getItemByOrderPropertyId(42)->setValue($res->orderId);
          self::$paymentHandlerDisallow = true;
          $order->save();
        } else {

          try {
            $bot = new \TelegramBot\Api\BotApi(self::TELEGRAM_BOT_ID);
            $bot->sendMessage(self::TELEGRAM_CHAT_ID, "Ошибка выгрузки Kam-Zu-Mi: самовывоз, оплата не на сайте");
            $bot->sendMessage(self::TELEGRAM_CHAT_ID, $iikoOrder->toText());
          } catch (\TelegramBot\Api\Exception $e) {
            AddMessage2Log($e->getMessage());
          }


          AddMessage2Log('Новый заказ, самовывоз, оплата не на сайте');
          AddMessage2Log($res);
          \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "RM_ADMIN_IIKO",
            "LID" => "s2",
            "C_FIELDS" => array(
              "EMAIL" => "admin@201086.ru",
              "USER_ID" => 1,
              "MESSAGE" => "Ошибка выгрузки заказа в iiko",
            ),
          ));
        }
      } elseif ($deliveryId == 18) { // Доставка                
        $propertyCollection = $order->getPropertyCollection();

        $name = $propertyCollection->getItemByOrderPropertyId(32) ? $propertyCollection->getItemByOrderPropertyId(32)->getField('VALUE') : "";
        $phone = $propertyCollection->getItemByOrderPropertyId(33) ? $propertyCollection->getItemByOrderPropertyId(33)->getField('VALUE') : "";
        $city = $propertyCollection->getItemByOrderPropertyId(35) ? $propertyCollection->getItemByOrderPropertyId(35)->getField('VALUE') : "";
        $street = $propertyCollection->getItemByOrderPropertyId(36) ? $propertyCollection->getItemByOrderPropertyId(36)->getField('VALUE') : "";
        $home = $propertyCollection->getItemByOrderPropertyId(37) ? $propertyCollection->getItemByOrderPropertyId(37)->getField('VALUE') : "";
        $housing = $propertyCollection->getItemByOrderPropertyId(38) ? $propertyCollection->getItemByOrderPropertyId(38)->getField('VALUE') : "";
        $apartment = $propertyCollection->getItemByOrderPropertyId(39) ? $propertyCollection->getItemByOrderPropertyId(39)->getField('VALUE') : "";
        $personsCount = $propertyCollection->getItemByOrderPropertyId(41) ? $propertyCollection->getItemByOrderPropertyId(41)->getField('VALUE') : "";
        $comment = $order->getField('USER_DESCRIPTION');

        $iiko = new IikoApi();
        $orgList = $iiko->getOrganizationList();
        $organization = new IikoOrganization($orgList[0]);
        $iiko->setOrganization($organization);


        $products = IikoApi::getIikoBasket($order->getBasket());

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

        $iikoOrder = $iiko->createOrder();
        $iikoOrder->phone = $phone;
        $iikoOrder->personsCount = $personsCount;
        $iikoOrder->comment = $comment;
        $iikoOrder->products = $products;
        $iikoOrder->setCustomer($customer);
        $iikoOrder->setAddress($address);

        $res = $iiko->sendOrder($iikoOrder);
        //$res = array('orderId' => 'complete');



        if ($res->orderId) {
          $propertyCollection->getItemByOrderPropertyId(42)->setValue($res->orderId);
          self::$paymentHandlerDisallow = true;
          $order->save();
        } else {

          try {
            $bot = new \TelegramBot\Api\BotApi(self::TELEGRAM_BOT_ID);
            $bot->sendMessage(self::TELEGRAM_CHAT_ID, "Ошибка выгрузки Kam-Zu-Mi: доставка, оплата не на сайте");
            $bot->sendMessage(self::TELEGRAM_CHAT_ID, $iikoOrder->toText());
          } catch (\TelegramBot\Api\Exception $e) {
            AddMessage2Log($e->getMessage());
          }


          AddMessage2Log('Новый заказ, доставка, оплата не на сайте');
          AddMessage2Log($res);
          \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "RM_ADMIN_IIKO",
            "LID" => "s2",
            "C_FIELDS" => array(
              "EMAIL" => "admin@201086.ru",
              "USER_ID" => 1,
              "MESSAGE" => "Ошибка выгрузки заказа в iiko",
            ),
          ));
        }
      }
    }

    // Онлайн-оплата - заказ оплачен        
    if ($paysystemId == $onlinePaymentSystemId && $order->getField('PAYED') == 'Y' && $oldValues['PAYED'] == 'N') {
      if ($deliveryId == 16) {
        $propertyCollection = $order->getPropertyCollection();

        $name = $propertyCollection->getItemByOrderPropertyId(32) ? $propertyCollection->getItemByOrderPropertyId(32)->getField('VALUE') : "";
        $phone = $propertyCollection->getItemByOrderPropertyId(33) ? $propertyCollection->getItemByOrderPropertyId(33)->getField('VALUE') : "";
        $personsCount = $propertyCollection->getItemByOrderPropertyId(41) ? $propertyCollection->getItemByOrderPropertyId(41)->getField('VALUE') : "";
        $comment = $order->getField('USER_DESCRIPTION');

        $paymentTypeCode = Option::get(self::MODULE_ID, "payment_type_code");
        if (!$paymentTypeCode) {
          return true;
        }

        $iiko = new IikoApi();
        $orgList = $iiko->getOrganizationList();
        $organization = new IikoOrganization($orgList[0]);
        $iiko->setOrganization($organization);

        $products = IikoApi::getIikoBasket($order->getBasket());

        $customer = $iiko->createCustomer();
        $customer->name = $name;
        $customer->phone = $phone;

        $address = array(
          'city' => "Калининград",
          'street' => "Аксакова",
          'home' => "100",
          'housing' => "",
          'apartment' => "1"
        );

        $paymentItem = array(
          'sum' => $order->getSumPaid(),
          'paymentType' => array('code' => $paymentTypeCode),
          'isProcessedExternally' => true,
          'isPreliminary' => false,
          'isExternal' => true,
          'additionalData' => "",
          'chequeAdditionalInfo' => array(
            'needReceipt' => true,
            'phone' => $phone
          ),
          'isFiscalizedExternally' => false
        );

        $iikoOrder = $iiko->createOrder();

        $iikoOrder->phone = $phone;
        $iikoOrder->isSelfService = true;
        $iikoOrder->products = $products;
        $iikoOrder->personsCount = $personsCount;
        $iikoOrder->comment = $comment;

        $iikoOrder->setCustomer($customer);
        $iikoOrder->setAddress($address);
        $iikoOrder->setPaymentItems($paymentItem);

        $res = $iiko->sendOrder($iikoOrder);
        //$res = array('orderId' => 'complete');

        if ($res->orderId) {
          //$propertyCollection->getItemByOrderPropertyId(42)->setValue($res->orderId);
          $propertyCollection->getItemByOrderPropertyId(42)->setValue($res->orderId);
          self::$paymentHandlerDisallow = true;
          $order->save();
        } else {

          try {
            $bot = new \TelegramBot\Api\BotApi(self::TELEGRAM_BOT_ID);
            $bot->sendMessage(self::TELEGRAM_CHAT_ID, "Ошибка выгрузки Kam-Zu-Mi: самовывоз, оплата на сайте");
            $bot->sendMessage(self::TELEGRAM_CHAT_ID, $iikoOrder->toText());
          } catch (\TelegramBot\Api\Exception $e) {
            AddMessage2Log($e->getMessage());
          }


          AddMessage2Log('Новый заказ, самовывоз, оплата на сайте');
          AddMessage2Log($res);
          \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "RM_ADMIN_IIKO",
            "LID" => "s2",
            "C_FIELDS" => array(
              "EMAIL" => "admin@201086.ru",
              "USER_ID" => 1,
              "MESSAGE" => "Ошибка выгрузки заказа в iiko",
            ),
          ));
        }

        return true;
      } elseif ($deliveryId == 18) {
        $propertyCollection = $order->getPropertyCollection();

        $name = $propertyCollection->getItemByOrderPropertyId(32) ? $propertyCollection->getItemByOrderPropertyId(32)->getField('VALUE') : "";
        $phone = $propertyCollection->getItemByOrderPropertyId(33) ? $propertyCollection->getItemByOrderPropertyId(33)->getField('VALUE') : "";
        $city = $propertyCollection->getItemByOrderPropertyId(35) ? $propertyCollection->getItemByOrderPropertyId(35)->getField('VALUE') : "";
        $street = $propertyCollection->getItemByOrderPropertyId(36) ? $propertyCollection->getItemByOrderPropertyId(36)->getField('VALUE') : "";
        $home = $propertyCollection->getItemByOrderPropertyId(37) ? $propertyCollection->getItemByOrderPropertyId(37)->getField('VALUE') : "";
        $housing = $propertyCollection->getItemByOrderPropertyId(38) ? $propertyCollection->getItemByOrderPropertyId(38)->getField('VALUE') : "";
        $apartment = $propertyCollection->getItemByOrderPropertyId(39) ? $propertyCollection->getItemByOrderPropertyId(39)->getField('VALUE') : "";
        $personsCount = $propertyCollection->getItemByOrderPropertyId(41) ? $propertyCollection->getItemByOrderPropertyId(41)->getField('VALUE') : "";
        $comment = $order->getField('USER_DESCRIPTION');

        $paymentTypeCode = Option::get(self::MODULE_ID, "payment_type_code");
        if (!$paymentTypeCode) {
          return true;
        }

        $iiko = new IikoApi();
        $orgList = $iiko->getOrganizationList();
        $organization = new IikoOrganization($orgList[0]);
        $iiko->setOrganization($organization);


        $products = IikoApi::getIikoBasket($order->getBasket());
        //AddMessage2Log($products);
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

        $paymentItem = array(
          'sum' => $order->getSumPaid(),
          'paymentType' => array('code' => $paymentTypeCode),
          'isProcessedExternally' => true,
          'isPreliminary' => false,
          'isExternal' => true,
          'additionalData' => "",
          'chequeAdditionalInfo' => array(
            'needReceipt' => true,
            'phone' => $phone
          ),
          'isFiscalizedExternally' => false
        );

        $iikoOrder = $iiko->createOrder();
        $iikoOrder->phone = $phone;
        $iikoOrder->personsCount = $personsCount;
        $iikoOrder->comment = $comment;
        $iikoOrder->products = $products;
        $iikoOrder->setCustomer($customer);
        $iikoOrder->setAddress($address);
        $iikoOrder->setPaymentItems($paymentItem);

        $res = $iiko->sendOrder($iikoOrder);
        //$res = array('orderId' => 'complete');



        if ($res->orderId) {
          $propertyCollection->getItemByOrderPropertyId(42)->setValue($res->orderId);
          self::$paymentHandlerDisallow = true;
          $order->save();
        } else {

          try {
            $bot = new \TelegramBot\Api\BotApi(self::TELEGRAM_BOT_ID);
            $bot->sendMessage(self::TELEGRAM_CHAT_ID, "Ошибка выгрузки Kam-Zu-Mi: доставка, оплата на сайте");
            $bot->sendMessage(self::TELEGRAM_CHAT_ID, $iikoOrder->toText());
          } catch (\TelegramBot\Api\Exception $e) {
            AddMessage2Log($e->getMessage());
          }


          AddMessage2Log('Новый заказ, доставка, оплата на сайте');
          AddMessage2Log($res);
          \Bitrix\Main\Mail\Event::send(array(
            "EVENT_NAME" => "RM_ADMIN_IIKO",
            "LID" => "s2",
            "C_FIELDS" => array(
              "EMAIL" => "admin@201086.ru",
              "USER_ID" => 1,
              "MESSAGE" => "Ошибка выгрузки заказа в iiko",
            ),
          ));
        }


        return true;
      }
    }
    return true;
  }
}
