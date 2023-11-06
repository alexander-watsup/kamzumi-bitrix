<?

namespace Sale\Handlers\Delivery;

use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Delivery\Services\Base;
use Reklamafia\Iiko\IikoApi;
use Reklamafia\Iiko\IikoOrganization;
use Reklamafia\Iiko\IikoProduct;
use Reklamafia\Iiko\IikoOrder;

class IikoV2Handler extends Base
{
    public static function getClassTitle()
    {
        return 'Доставка iiko (v2)';
    }

    public static function getClassDescription()
    {
        return 'Доставка iiko (v2)';
    }

    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment)
    {
        $result = new CalculationResult();

        $result->setDeliveryPrice(0);
        return $result;


        /*
        $order = $shipment->getCollection()->getOrder();
        $propertyCollection = $order->getPropertyCollection();


        try {
            $name = $propertyCollection->getItemByOrderPropertyCode('NAME')->getValue();
            $phone = $propertyCollection->getItemByOrderPropertyCode('PHONE')->getValue();
            $city = $propertyCollection->getItemByOrderPropertyCode('CITY')->getValue();
            $street = $propertyCollection->getItemByOrderPropertyCode('STREET')->getValue();
            $home = $propertyCollection->getItemByOrderPropertyCode('HOME')->getValue();
            $housing = $propertyCollection->getItemByOrderPropertyCode('HOUSING')->getValue();
            $apartment = $propertyCollection->getItemByOrderPropertyCode('APARTMENT')->getValue();
        } catch (\Throwable $t) {
            $result->setDescription("Для расчёта доставки заполните обязательные поля");
            $result->addError(new \Bitrix\Main\Error("Не заполнены обязательные поля"));
            return $result;
        }

        if ($name == "" || $phone == "" || $city == "" || $street == "" || $home == "" || $apartment == "") {
            $result->setDescription("Для расчёта доставки заполните обязательные поля");
            $result->addError(new \Bitrix\Main\Error("Не заполнены обязательные поля"));
            return $result;
        }


        /*

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
        $order->phone = $phone;
        $order->products = $products;
        $order->setCustomer($customer);
        $order->setAddress($address);

        // Получаем данные
        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $key = crc32($city . $street . $home . $housing . $apartment);

        if ($cache->initCache(300, $key)) {
            $deliveryInfo = json_decode($cache->getVars()[$key]);
        } elseif ($cache->startDataCache()) {
            $deliveryInfo = $iiko->getDeliveryInfo($order);
            $cache->endDataCache(array($key => json_encode($deliveryInfo)));
        }
        // ====

        //AddMessage2Log($deliveryInfo);

        if ($deliveryInfo->resultState === 0) {
            $deliveryDuration = $deliveryInfo->deliveryDurationInMinutes;
            $deliveryPrice = 0;

            if ($deliveryInfo->deliveryRestriction) {
                $deliveryDuration += $deliveryInfo->deliveryRestriction->deliveryDurationInMinutes;
            }

            if ($deliveryInfo->deliveryServiceProductInfo) {
                $deliveryPrice += $deliveryInfo->deliveryServiceProductInfo->productSum;
            }

            $result->setDeliveryPrice($deliveryPrice);
            $result->setPeriodDescription($deliveryDuration . ' минут');
        } else if ($deliveryInfo->resultState === 1) {
            $result->setDescription("Ваш заказ слишком мал, мы не сможем его доставить");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else if ($deliveryInfo->resultState === 2) {
            $result->setDescription("Мы сейчас закрыты и не сможем организовать доставку");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else if ($deliveryInfo->resultState === 3) {
            $result->setDescription("Мы доставим ваш заказ, но доставка будет платная, стоимость уточняйте по телефону 31-31-02");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else if ($deliveryInfo->resultState === 4) {
            $result->setDescription("В вашем заказе есть блюда, которые мы возможно не сможем доставить. Позвоните нам.");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else if ($deliveryInfo->resultState === 0) {
            $result->setDescription("В вашем заказе есть блюда, которые мы возможно не сможем доставить. Позвоните нам.");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else {
            //$result->setDescription("Для расчёта доставки заполните обязательные поля");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        }

        
        $result->setDeliveryPrice(100);
        return $result;
        */
    }

    protected function getConfigStructure()
    {
        return array();
        /*
            return array(
                "MAIN" => array(
                    "TITLE" => 'Настройка обработчика',
                    "DESCRIPTION" => 'Настройка обработчика',"ITEMS" => array(
                        "PRICE" => array(
                                    "TYPE" => "NUMBER",
                                    "MIN" => 0,
                                    "NAME" => 'Стоимость доставки за грамм'
                        )
                    )
                )
            );*/
    }

    public function isCalculatePriceImmediately()
    {
        return false;
    }

    public static function whetherAdminExtraServicesShow()
    {
        return false;
    }
}
