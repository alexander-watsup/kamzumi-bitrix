<?

namespace Reklamafia\Agents;

class IIKO
{

    public static function updateOrderStatuses()
    {
        \CModule::IncludeModule('sale');

        $arrComposeStatuses = array(
            'Новая' => 'IE', //новая
            'Ждет отправки' => 'IF', //Ждет отправки
            'В пути' => 'IG', //В пути
            'Закрыта' => 'IH', //Закрыта
            'Отменена' => 'II', //Отменена
            'Доставлена' => 'IJ', //Доставлена
            'Не подтверждена' => 'IK', //Не подтверждена
            'Готовится' => 'IL', //Готовится
            'Готово' => 'IM', //Готово
        );

        $iiko = new \Reklamafia\Exchange\IIKO();

        $nTopCount = array('nTopCount' => 20);
        $arFilter = array(
            "=STATUS_ID" => array("IE", "IF", "IG", "IJ", "IL", "IM"),
            "!PROPERTY_VAL_BY_CODE_IIKO_ID" => false,
        );

        $dbRes = \CSaleOrder::GetList(array("ID" => "DESC"), $arFilter, false, $nTopCount);

        while ($arOrder = $dbRes->fetch()) {

            $order = \Bitrix\Sale\Order::load($arOrder['ID']);
            $propertyCollection = $order->getPropertyCollection();
            $iikoId = $propertyCollection->getItemByOrderPropertyCode('IIKO_ID')->getValue();

            //$iikoOrderInfo = $iiko->getOrder("186355dc-7e60-4b44-aa1a-9120efdd07ff");
            $iikoOrderInfo = $iiko->getOrder($iikoId);




            if ($iikoOrderInfo->code === "110" && $iikoOrderInfo->httpStatusCode == 500) {
                $order->setField("STATUS_ID", "IZ");
                $order->save();
            } else {
                $likoOrderStatus = $iikoOrderInfo->status;
                $orderNewStatus = $arrComposeStatuses[$likoOrderStatus];


                if ($orderNewStatus && $order->getField("STATUS_ID") !== $orderNewStatus) {
                    $order->setField("STATUS_ID", $orderNewStatus);
                    $order->save();
                }
            }

            //echo $arOrder['ID'];
            //var_dump($iikoOrderInfo);
        }

        return "\Reklamafia\Agents\IIKO::updateOrderStatuses();";
    }

    public static function uploadOrders()
    {

        /*

        IA ожидает выгрузки IIKO
        IB ожидает оплаты на сайте
        IC ошибка выгрузки IIKO
        IZ не найден в IIKO
        
        'Новая' => 'IE',//новая
        'Ждет отправки' => 'IF',//Ждет отправки
        'В пути' => 'IG',//В пути
        'Закрыта' => 'IH',//Закрыта
        'Отменена' => 'II',//Отменена
        'Доставлена' => 'IJ',//Доставлена
        'Не подтверждена' => 'IK',//Не подтверждена
        'Готовится' => 'IL',//Готовится
        'Готово' => 'IM',//Готово
     */


        \CModule::IncludeModule('sale');

        $iiko = new \Reklamafia\Exchange\IIKO();

        $dbRes = \Bitrix\Sale\Order::getList([
            'filter' => [
                'STATUS_ID' => array("IA")
            ],
            'order' => ['ID' => 'DESC']
        ]);

        while ($arrOrder = $dbRes->fetch()) {
            //var_dump($order);

            $order = \Bitrix\Sale\Order::load($arrOrder['ID']);
            $basket = $order->getBasket()->getOrderableItems();
            $propertyCollection = $order->getPropertyCollection();
            $paymentCollection = $order->getPaymentCollection();



            $params = [
                "order" => [
                    'date' => null,
                    'items' => [],
                    'paymentItems' => [],
                    "phone" => $propertyCollection->getItemByOrderPropertyId(51)->getValue(),
                    "customerName" => $propertyCollection->getItemByOrderPropertyId(50)->getValue(),
                    "comment" => $arrOrder["USER_DESCRIPTION"],
                    "personsCount" => $propertyCollection->getItemByOrderPropertyId(58)->getValue(),
                    "marketingSource" => "kam-zu-mi.ru",
                ]
            ];

            foreach ($basket as $item) {
                $item = [
                    'id' => $item->getField("PRODUCT_XML_ID"),
                    'amount' => $item->getQuantity(),
                ];
                $params['order']['items'][] = $item;
            }

            foreach ($paymentCollection as $payment) {
                if ($payment->isPaid()) {
                    $params['order']['paymentItems'][] = [
                        'sum' => $payment->getSum(),
                        'paymentType' => ['code' => "SITE"],
                        'isProcessedExternally' => true,
                        'isExternal' => true,
                        'isFiscalizedExternally' => false
                    ];
                }
            }

            if ($arrOrder['DELIVERY_ID'] == 22) {
                $params['order']['isSelfService'] = false;
                $params['order']["address"] = [
                    'city' => $propertyCollection->getItemByOrderPropertyId(52)->getValue(),
                    'street' => $propertyCollection->getItemByOrderPropertyId(53)->getValue(),
                    'home' => $propertyCollection->getItemByOrderPropertyId(54)->getValue(),
                    'housing' => $propertyCollection->getItemByOrderPropertyId(55)->getValue(),
                    'apartment' => $propertyCollection->getItemByOrderPropertyId(56)->getValue()
                ];

                // КОСТЫЛЬ для IIKO. Поле "корпус" сохраняем в комментарий и очищаем
                // Потому что когда установлен корпус, адрес не находится на картах гугл и заказ не распределяется между терминалами
                if ($params['order']["address"]['housing'] !== "") {
                    $params['order']["comment"] = "Корпус:" . $params['order']["address"]['housing'] . ". " . $params['order']["comment"];
                    $params['order']["address"]['housing'] = "";
                }
                // END
            }

            if ($arrOrder['DELIVERY_ID'] == 23) {
                $params['order']['isSelfService'] = true;
                $params['deliveryTerminalId'] = $propertyCollection->getItemByOrderPropertyId(57)->getValue();
            }

            $retryCount = $propertyCollection->getItemByOrderPropertyCode('IIKO_RETRYCOUNT')->getValue();



            if ($retryCount > 10) {
                $order->setField("STATUS_ID", "IC");
                $order->save();
                \Reklamafia\Messaging\Telegram::sendMessage("IIKO не принимает заказ ID " . $arrOrder['ID']);
            } else {
                $propertyCollection->getItemByOrderPropertyCode('IIKO_RETRYCOUNT')->setValue($retryCount + 1);
                $order->save();


                $result =  $iiko->createOrder($params);
                if ($result->orderId) {
                    $propertyCollection->getItemByOrderPropertyCode('IIKO_ID')->setValue($result->orderId);
                    $order->setField("STATUS_ID", "IE");
                    $order->save();
                } else {
                    $message = "IIKO выгрузка заказа " . $arrOrder['ID'] . ", попытка " . $retryCount . " не удалась. Попробуем ещё раз.\r\n";
                    $message .= json_encode($result);
                    \Reklamafia\Messaging\Telegram::sendMessage($message);
                }
            }
        }

        return "\Reklamafia\Agents\IIKO::uploadOrders();";
    }
}
