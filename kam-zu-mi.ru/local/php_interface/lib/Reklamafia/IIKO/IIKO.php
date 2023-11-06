<?

namespace Reklamafia\Exchange;

class IIKO
{
    const IIKO_BASE_URI = 'https://iiko.biz:9900/api/0/';

    private $login;
    private $password;
    private $token;
    private $orgId;

    public function __construct()
    {
        $this->login = "kamzumiCC";
        $this->password = "AmUZcvHDVc";
        $this->token = null;
        $this->orgId = "fe470000-906b-0025-30e5-08d920568b0f";

        $this->createToken();
    }

    private function createToken()
    {
        if ($this->token) return;

        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $key = 'IIKOToken';
        $life_time = 10 * 60;

        if ($cache->initCache($life_time, $key)) {
            $token = $cache->getVars()[$key];
        } else {
            $cache->startDataCache();
            $url = $this::IIKO_BASE_URI . "auth/access_token?user_id=" . $this->login . "&user_secret=" . $this->password;
            $httpClient = new \Bitrix\Main\Web\HttpClient();
            $result = $httpClient->get($url);
            $token = json_decode($result);
            $cache->endDataCache(array($key => $token));
        }

        $this->token = $token;
    }

    public function getTerminals()
    {
        if (!$this->token) return null;

        $result = [];

        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $key = 'IIKOTerminals';
        $life_time = 7 * 24 * 60 * 60;

        if ($cache->initCache($life_time, $key)) {
            $result = json_decode($cache->getVars()[$key]);
        } else {
            $cache->startDataCache();
            $url = $this::IIKO_BASE_URI . "deliverySettings/getDeliveryTerminals?access_token=" . $this->token . "&organization=" . $this->orgId;
            $httpClient = new \Bitrix\Main\Web\HttpClient();
            $result = $httpClient->get($url);
            $jsonResponse = json_decode($result);

            $result = [];
            foreach ($jsonResponse->deliveryTerminals as $terminal) {
                $result[] = [
                    'id' => $terminal->deliveryTerminalId,
                    'name' => $terminal->deliveryRestaurantName
                ];
            }

            $cache->endDataCache(array($key => json_encode($result, JSON_UNESCAPED_UNICODE)));
        }

        return $result;
    }

    public function getCities()
    {
        if (!$this->token) return null;

        $result = [];

        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $key = 'IIKOCities';
        $life_time = 7 * 24 * 60 * 60;
        // $cache->forceRewriting(true);
        if ($cache->initCache($life_time, $key)) {
            $result = json_decode($cache->getVars()[$key]);
        } else {
            $cache->startDataCache();
            $url = $this::IIKO_BASE_URI . "cities/citiesList?access_token=" . $this->token . "&organization=" . $this->orgId;
            $httpClient = new \Bitrix\Main\Web\HttpClient();
            $result = $httpClient->get($url);
            $jsonResponse = json_decode($result);

            $result = [];
            foreach ($jsonResponse as $terminal) {
                $result[] = [
                    'id' => $terminal->id,
                    'name' => $terminal->name
                ];
            }

            $cache->endDataCache(array($key => json_encode($result, JSON_UNESCAPED_UNICODE)));
        }

        return $result;
    }

    public function getStreets($cityId = null)
    {
        if (!$this->token) return null;
        if (!$cityId) return [];

        $result = [];

        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $key = 'IIKOStreets' . $cityId;
        $life_time = 7 * 24 * 60 * 60;
        //$cache->forceRewriting(true);
        if ($cache->initCache($life_time, $key)) {
            $result = json_decode($cache->getVars()[$key]);
        } else {
            $cache->startDataCache();
            $url = $this::IIKO_BASE_URI . "streets/streets?access_token=" . $this->token . "&organization=" . $this->orgId . "&city=" . $cityId;
            $httpClient = new \Bitrix\Main\Web\HttpClient();
            $result = $httpClient->get($url);
            $jsonResponse = json_decode($result);

            $result = [];
            foreach ($jsonResponse as $street) {
                $result[] = [
                    'id' => $street->id,
                    'name' => $street->name
                ];
            }

            $cache->endDataCache(array($key => json_encode($result, JSON_UNESCAPED_UNICODE)));
        }

        return $result;
    }

    public function checkOrder($params = [])
    {
        if (!$this->token) return null;
        if (!$params) return [];

        $result = [];

        $postParams = [
            'organization' => $this->orgId,
            'order' =>  $params['order'],
            'date' =>  $params['date']
        ];

        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $key = 'IIKOCheckOrder' . hash('md5', json_encode($postParams));
        $life_time = 7 * 24 * 60 * 60;
        //$cache->forceRewriting(true);
        if ($cache->initCache($life_time, $key)) {
            $result = json_decode($cache->getVars()[$key]);
        } else {
            $cache->startDataCache();
            $url = $this::IIKO_BASE_URI . "orders/checkCreate?access_token=" . $this->token;
            $httpClient = new \Bitrix\Main\Web\HttpClient();
            $httpClient->setHeader('Content-Type', 'application/json', true);
            $result = $httpClient->post($url, json_encode($postParams, JSON_UNESCAPED_UNICODE));
            $jsonResponse = json_decode($result);

            $result = [];
            $result['code'] = $jsonResponse->resultState;

            if ($jsonResponse->resultState === 0) {
                $result['deliveryPrice'] = $jsonResponse->deliveryServiceProductInfo->productSum;
            }

            $cache->endDataCache(array($key => json_encode($result, JSON_UNESCAPED_UNICODE)));
        }

        return $result;
    }

    public function createOrder($params = [])
    {
        if (!$this->token) return null;
        if (!$params) return [];

        $params['organization'] = $this->orgId;
        $url = $this::IIKO_BASE_URI . 'orders/add?access_token=' . $this->token . '&request_timeout="00:02:00"';
        $httpClient = new \Bitrix\Main\Web\HttpClient();
        $httpClient->setHeader('Content-Type', 'application/json', true);
        $result = $httpClient->post($url, json_encode($params, JSON_UNESCAPED_UNICODE));
        $jsonResponse = json_decode($result);

        return $jsonResponse;
    }

    public function getOrder($orderId)
    {
        if (!$this->token) return null;
        if (!$orderId) return [];

        $url = $this::IIKO_BASE_URI . "orders/info?access_token=" . $this->token . "&organization=" . $this->orgId . "&order=" . $orderId . "&request_timeout=0:0:30";
        $httpClient = new \Bitrix\Main\Web\HttpClient();
        $result = $httpClient->get($url);
        $jsonResponse = json_decode($result);

        return $jsonResponse;
    }
}
