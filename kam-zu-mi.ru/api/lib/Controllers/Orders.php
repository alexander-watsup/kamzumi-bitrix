<?php

namespace KamZuMi\Api\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Orders
{

    const DELIVERY_PICKUP_ID = 23;
    const DELIVERY_COURIER_ID = 22;

    const PAYMENT_ONSITE_SB_ID = 14;
    const PAYMENT_ONDELIVERY_CASH_ID = 18;

    const PERSON_TYPE = 5;

    public function createOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                die(); // !!! Achtung                
            }
        }

        \Bitrix\Main\Loader::includeModule("sale");

        global $USER;
        $userId = $USER->GetID();

        if (!$userId) {

            $user = \Bitrix\Main\UserTable::getList(array(
                'filter' => array(
                    '=LOGIN' => "user" . $contents['phone'],
                ),
                'limit' => 1,
            ))->fetch();

            if ($user) {
                $userId = $user['ID'];
                $USER->Authorize($userId);
            } else {
                $user = new \CUser;
                $newPassword = randString(7);
                $arFields = array(
                    "NAME"              => $contents['name'],
                    "LOGIN"             => "user" . $contents['phone'],
                    "WORK_PHONE" => $contents['phone'],
                    "ACTIVE"            => "Y",
                    "GROUP_ID"          => array(6),
                    "PASSWORD"          =>  $newPassword,
                    "CONFIRM_PASSWORD"  =>  $newPassword
                );

                $userId = $user->Add($arFields);

                $phone = \Bitrix\Main\UserPhoneAuthTable::normalizePhoneNumber($contents['phone']);
                \Bitrix\Main\UserPhoneAuthTable::add([
                    'USER_ID' => $userId,
                    'PHONE_NUMBER' => $phone,
                ]);

                $USER->Authorize($userId, true);
            }
        }

        $userBasket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
        $userBasketItems = $userBasket->getOrderableItems();

        if (count($userBasketItems) < 1) {
            $result = ['error' => 1, 'message' => "Корзина пуста"];
            $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

            return $response
                ->withHeader('Content-Type', 'application/json');
        }

        $basket = \Bitrix\Sale\Basket::create(SITE_ID);
        foreach ($userBasketItems as $product) {
            $basket->addItem($product);
        }

        $order = \Bitrix\Sale\Order::create(SITE_ID, $userId);
        $order->setPersonTypeId(self::PERSON_TYPE);
        $order->setBasket($basket);


        // ДОСТАВКА 
        $deliveryServiceId = self::DELIVERY_PICKUP_ID;
        $deliveryPrice = 0;
        if (isset($contents['deliveryType']) && $contents['deliveryType'] === "courier") {
            $deliveryServiceId = self::DELIVERY_COURIER_ID;
            if (isset($contents['deliveryPrice'])) {
                $deliveryPrice = $contents['deliveryPrice'];
            }
        }

        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem(
            \Bitrix\Sale\Delivery\Services\Manager::getObjectById($deliveryServiceId)
        );
        $shipment->setBasePriceDelivery($deliveryPrice);

        // Отгрузка
        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        foreach ($basket as $basketItem) {
            $item = $shipmentItemCollection->createItem($basketItem);
            $item->setQuantity($basketItem->getQuantity());
        }

        // Оплата
        $paymentServiceId = self::PAYMENT_ONDELIVERY_CASH_ID;
        if ($contents['paymentType'] === "onSiteSB")
            $paymentServiceId = self::PAYMENT_ONSITE_SB_ID;
        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->createItem(
            \Bitrix\Sale\PaySystem\Manager::getObjectById($paymentServiceId)
        );
        $payment->setField("SUM", $order->getPrice());
        $payment->setField("CURRENCY", $order->getCurrency());
        if ($contents['paymentType'] === "onSiteSB") {
            $order->setField("STATUS_ID", "IB");
        } else {
            $order->setField("STATUS_ID", "IA");
        }



        // Поля заказа
        $order->setField('USER_DESCRIPTION', $contents['comment']);
        //$order->setField('COMMENTS', $contents['comment']);
        $propertyCollection = $order->getPropertyCollection();
        $propertyCollection->getItemByOrderPropertyCode('NAME')->setValue($contents['name']);
        $propertyCollection->getItemByOrderPropertyCode('PHONE')->setValue($contents['phone']);
        $propertyCollection->getItemByOrderPropertyCode('GUESTCOUNT')->setValue($contents['guestCount']);
        $propertyCollection->getItemByOrderPropertyCode('IIKO_RETRYCOUNT')->setValue(0);

        if (isset($contents['deliveryType']) && $contents['deliveryType'] === "courier") {
            $propertyCollection->getItemByOrderPropertyCode('CITY')->setValue($contents['city']);
            $propertyCollection->getItemByOrderPropertyCode('STREET')->setValue($contents['street']);
            $propertyCollection->getItemByOrderPropertyCode('HOUSE')->setValue($contents['house']);
            $propertyCollection->getItemByOrderPropertyCode('HOUSING')->setValue($contents['housing']);
            $propertyCollection->getItemByOrderPropertyCode('APARTMENT')->setValue($contents['apartment']);
        }

        if (isset($contents['deliveryType']) && $contents['deliveryType'] === "pickup") {
            $propertyCollection->getItemByOrderPropertyCode('PICKUPPLACE')->setValue($contents['pickupPointId']);
        }

        $result = $order->save();




        if (!$result->isSuccess()) {
            //$result->getErrors();
        }

//https://kam-zu-mi.ru/personal/basket/order.php?ORDER_ID=3855
        $redirectUrl = "https://kam-zu-mi.ru/personal/basket/order.php?ORDER_ID=" . $order->getId();

        $result = ['error' => 0, 'orderId' => $order->getId(), 'redirectUrl' => $redirectUrl];
        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
