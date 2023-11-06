<?

namespace Sale\Handlers\Delivery;

use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Delivery\Services\Base;

class PickupHandler extends Base
{
    public static function getClassTitle()
    {
        return 'Самовывоз (v2)';
    }

    public static function getClassDescription()
    {
        return 'Самовывоз (v2)';
    }

    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment)
    {
        $result = new CalculationResult();
        $result->setDeliveryPrice(0);
        return $result;
    }

    protected function getConfigStructure()
    {
        return array();
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
