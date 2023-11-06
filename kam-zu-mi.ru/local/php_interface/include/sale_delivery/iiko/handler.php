<?
namespace Sale\Handlers\Delivery;

use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Delivery\Services\Base;
use Reklamafia\Iiko\IikoApi;
use Reklamafia\Iiko\IikoOrganization;
use Reklamafia\Iiko\IikoProduct;
use Reklamafia\Iiko\IikoOrder;

class IikoHandler extends Base
{
    public static function getClassTitle()
        {
            return 'Доставка iiko';
        }
        
    public static function getClassDescription()
        {
            return 'Доставка iiko';
        }
        
    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment)
    {
        $result = new CalculationResult();
            //$price = floatval($this->config["MAIN"]["PRICE"]);
            //$weight = floatval($shipment->getWeight()) / 1000;
        
            //$result->setDeliveryPrice(roundEx($price * $weight, 2));
            //$result->setPeriodDescription('1 день');

            

        //$shipment = $event->getParameter('SHIPMENT');
        $propertyCollection = $shipment->getCollection()->getOrder()->getPropertyCollection();

        //AddMessage2Log($propertyCollection);
       

        $name = $propertyCollection->getItemByOrderPropertyId(32)?$propertyCollection->getItemByOrderPropertyId(32)->getField('VALUE') : "";
        $phone = $propertyCollection->getItemByOrderPropertyId(33)?$propertyCollection->getItemByOrderPropertyId(33)->getField('VALUE') : "";
        $city = $propertyCollection->getItemByOrderPropertyId(35) ? $propertyCollection->getItemByOrderPropertyId(35)->getField('VALUE') : "";
        $street = $propertyCollection->getItemByOrderPropertyId(36) ? $propertyCollection->getItemByOrderPropertyId(36)->getField('VALUE') : "";
        $home = $propertyCollection->getItemByOrderPropertyId(37) ? $propertyCollection->getItemByOrderPropertyId(37)->getField('VALUE') : "";
        $housing = $propertyCollection->getItemByOrderPropertyId(38) ? $propertyCollection->getItemByOrderPropertyId(38)->getField('VALUE') : "";
        $apartment = $propertyCollection->getItemByOrderPropertyId(39) ? $propertyCollection->getItemByOrderPropertyId(39)->getField('VALUE') : "";

        if($name==""||$phone==""||$city==""||$street==""||$home==""||$apartment=="") {
            $result->setDescription("Для расчёта доставки заполните обязательные поля");
            $result->addError(new \Bitrix\Main\Error("Не заполнены обязательные поля"));
            return $result;
        }
        

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
        $key = crc32($city.$street.$home.$housing.$apartment);

        if ($cache->initCache(300, $key)) {            
            $deliveryInfo = json_decode($cache->getVars()[$key]);            
        } elseif ($cache->startDataCache()) {    
            $deliveryInfo = $iiko->getDeliveryInfo($order);
            $cache->endDataCache(array($key => json_encode($deliveryInfo)));            
        }                
        // ====
        
        //AddMessage2Log($deliveryInfo);
        
        if($deliveryInfo->resultState===0) {
            $deliveryDuration = $deliveryInfo->deliveryDurationInMinutes;
            $deliveryPrice = 0;

            if($deliveryInfo->deliveryRestriction) {
                $deliveryDuration += $deliveryInfo->deliveryRestriction->deliveryDurationInMinutes;
            }

            if($deliveryInfo->deliveryServiceProductInfo) {
                $deliveryPrice += $deliveryInfo->deliveryServiceProductInfo->productSum;
            }

            $result->setDeliveryPrice($deliveryPrice);
            $result->setPeriodDescription($deliveryDuration.' минут');
            
        } else if($deliveryInfo->resultState===1) {
            $result->setDescription("Ваш заказ слишком мал, мы не сможем его доставить");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else if($deliveryInfo->resultState===2) {
            $result->setDescription("Мы сейчас закрыты и не сможем организовать доставку");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else if($deliveryInfo->resultState===3) {
            $result->setDescription("Мы доставим ваш заказ, но доставка будет платная, стоимость уточняйте по телефону 31-31-02");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else if($deliveryInfo->resultState===4) {
            $result->setDescription("В вашем заказе есть блюда, которые мы возможно не сможем доставить. Позвоните нам.");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else if($deliveryInfo->resultState===0) {
            $result->setDescription("В вашем заказе есть блюда, которые мы возможно не сможем доставить. Позвоните нам.");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        } else {
            //$result->setDescription("Для расчёта доставки заполните обязательные поля");
            $result->addError(new \Bitrix\Main\Error("Ошибка расчёта заказа"));
        }
        
  
        return $result;

          
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
            return true;
        }
        
    public static function whetherAdminExtraServicesShow()
        {
            return true;
        }
}
?>