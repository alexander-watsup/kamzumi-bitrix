<?php

namespace Reklamafia\Iiko;

use Bitrix\Main\SystemException;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Diag\Debug;

require_once dirname(__FILE__) . '/../vendor/guzzle/index.php';

use Exception;

class IikoApi
{
    const MODULE_ID = "reklamafia.iiko";
    const IIKO_BASE_URI = 'https://iiko.biz:9900/api/0/';

    private $login;
    private $password;
    private $accessToken;

    private $organization;

    public function __construct(array $options = [])
    {
    
        if (!isset($options['login']) || !isset($options['password'])) {
            $this->login = Option::get(self::MODULE_ID, "iiko_user");
            $this->password = Option::get(self::MODULE_ID, "iiko_password");
        } else {
            $this->login = $options['login'];
            $this->password = $options['password'];
        }
        $this->createToken();
    }

    public function createToken()
    {
        $params = [
            'user_id' => $this->login,
            'user_secret' => $this->password
        ];

        $httpClient = new HttpClient();
        $httpClient->setHeader('Content-Type', 'application/json', true);
        $httpResponse = $httpClient->get(self::IIKO_BASE_URI . 'auth/access_token?' . http_build_query($params));

        if ($httpClient->getStatus() === 200) {
            $token = (string)$httpResponse;
            $this->accessToken = str_replace('"', '', $token);
        }

        return $this;
    }

    public function setBaseUri(string $uri)
    {
        $this->baseUri = $uri;
        return $this;
    }

    public function getToken()
    {
        return $this->accessToken;
    }

    public function createOrder()
    {
        return new IikoOrder($this->getGUID());
    }

    public function createProduct()
    {
        $product = new \stdClass();
        $product->id = $this->getGUID();
        return new IikoProduct($product);
    }

    public function createCustomer()
    {
        return new IikoCustomer($this->getGUID());
    }

    public function getGUID()
    {
        if (function_exists("com_create_guid"))
            return com_create_guid();

        mt_srand((float)microtime() * 10000); //optional for php 4.2.0 and up.
        $charId = md5(uniqid(rand(), true));
        $hyphen = chr(45); // "-"
        $uuid = substr($charId, 0, 8) . $hyphen
            . substr($charId, 8, 4) . $hyphen
            . substr($charId, 12, 4) . $hyphen
            . substr($charId, 16, 4) . $hyphen
            . substr($charId, 20, 12);

        return $uuid;
    }

    public function getOrganizationList()
    {
        $result = [];

        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $key = 'getOrganizationList';

        if ($cache->initCache(300, $key)) {
            $result = json_decode($cache->getVars()[$key]);
        } elseif (!is_null($this->accessToken) && $cache->startDataCache()) {
            $params = [
                'access_token' => $this->accessToken,
                'requestTimeout' => '00:02:00'
            ];

            $httpClient = new HttpClient();
            $httpClient->setHeader('Content-Type', 'application/json', true);
            $httpResponse = $httpClient->get(self::IIKO_BASE_URI . 'organization/list?' . http_build_query($params));

            if ($httpClient->getStatus() === 200) {
                $result = json_decode((string)$httpResponse);
                $this->organizations = $result;
            }

            $cache->endDataCache(array($key => json_encode($result)));
        }

        return $result;
    }

    public function createOrganization($object, $setOrgAfterCreate = false)
    {
        if (isset($object->id)) {
            $organization = new IikoOrganization($object->id);
            $organization->isActive = $object->isActive;
            $organization->description = $object->description;
            $organization->phone = $object->contact->phone;
            $organization->email = $object->contact->email;
            $organization->name = $object->name;

            if ($setOrgAfterCreate === true) {
                $this->setOrganization($organization);
            }

            return $organization;
        }

        return false;
    }

    public function setOrganization(IikoOrganization $organization)
    {
        $this->organization = $organization;
    }

    public function getNomenclature()
    {
        if (is_null($this->organization)) {
            throw new Exception('Для получения продукции необходимы данные о организации. Используйте Api->setOrganization(Organization $organization)');
        }

        $result = [];
        $httpResponseJSON = [];
        $orgId = $this->organization->id;

        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $key = 'getNomenclature_' . $orgId;

        if ($cache->initCache(300, $key)) {
            $httpResponseJSON = json_decode($cache->getVars()[$key]);
        } elseif (!is_null($this->accessToken) && $cache->startDataCache()) {
            $params = [
                'access_token' => $this->accessToken,
                'organizationId' => $orgId
            ];

            $httpClient = new HttpClient();
            $httpClient->setHeader('Content-Type', 'application/json', true);
            $httpResponse = $httpClient->get(self::IIKO_BASE_URI . 'nomenclature/' . $orgId . '?' . http_build_query($params));
            $httpResponseJSON = json_decode($httpResponse);
            $cache->endDataCache(array($key => json_encode($httpResponseJSON)));
        }


        if (isset($httpResponseJSON->products)) {
            foreach ($httpResponseJSON->products as $product) {
                $result[] = new IikoProduct($product);
            }
        }

        return $result;
    }

    public function getGroups()
    {
        if (is_null($this->organization)) {
            throw new Exception('Для получения групп необходимы данные о организации. Используйте Api->setOrganization(Organization $organization)');
        }

        $result = [];
        $httpResponseJSON = [];
        $orgId = $this->organization->id;

        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $key = 'getGroups_' . $orgId;

        if ($cache->initCache(300, $key)) {
            $httpResponseJSON = json_decode($cache->getVars()[$key]);
        } elseif (!is_null($this->accessToken) && $cache->startDataCache()) {
            $params = [
                'access_token' => $this->accessToken,
                'organizationId' => $orgId
            ];

            $httpClient = new HttpClient();
            $httpClient->setHeader('Content-Type', 'application/json', true);
            $httpResponse = $httpClient->get(self::IIKO_BASE_URI . 'nomenclature/' . $orgId . '?' . http_build_query($params));
            $httpResponseJSON = json_decode($httpResponse);
            $cache->endDataCache(array($key => json_encode($httpResponseJSON)));
        }


        if (isset($httpResponseJSON->groups)) {
            foreach ($httpResponseJSON->groups as $group) {
                $result[] = $group;
            }
        }

        return $result;
    }

    public function getDeliveryInfo(IikoOrder $order)
    {
        if (empty($order->products)) {
            throw new SystemException('Property "products" of class Order can\'t be empty!');
        }

        if (empty($order->customer)) {
            throw new SystemException('Property "customer" of class Order can\'t be empty!');
        }

        if (empty($order->address)) {
            throw new SystemException('Property "address" of class Order can\'t be empty!');
        }


        $params = [
            'access_token' => $this->accessToken,
            'request_timeout' => '00:00:10',
            'organizationId' => $this->organization->id,
        ];

        $postParams = [
            'organization' => $this->organization->id,
            'customer' => $order->customer,
            'order' => [
                'id' => '',
                'date' =>  $order->date,
                'phone' => $order->customer->phone,
                'isSelfService' => 'false',
                'items' => $order->products,
                'address' => $order->address
            ]
        ];

        $httpClient = new HttpClient();
        $httpClient->setHeader('Content-Type', 'application/json', true);
        $httpResponse = $httpClient->post(self::IIKO_BASE_URI . 'orders/checkCreate?' . http_build_query($params), json_encode($postParams));
        $httpResponseJSON = json_decode($httpResponse);

        return $httpResponseJSON;
    }


    public function checkOrder(IikoOrder $order)
    {

        if (empty($order->products)) {
            throw new SystemException('Property "products" of class Order can\'t be empty!');
        }

        if (empty($order->customer)) {
            throw new SystemException('Property "customer" of class Order can\'t be empty!');
        }

        if (empty($order->address)) {
            throw new SystemException('Property "address" of class Order can\'t be empty!');
        }

        $productsItems = $this->prepareProducts($order->products);

        $params = [
            'access_token' => $this->accessToken,
            'request_timeout' => '00:02:00',
            'organizationId' => $this->organization->id,
        ];

        $postParams = [
            'organization' => $this->organization->id,
            'customer' => (array)$order->customer,
            'order' => [
                'id' => $order->id,
                'date' => $order->date,
                'phone' => $order->phone,
                'isSelfService' => $order->isSelfService,
                'items' => $productsItems,
                'address' => (array)$order->address
            ],
        ];

        $postParams['customer']['id'] = "";        
        $postParams['order']['id'] = "";
        $postParams['order']['items'] = [
            [
                "id" => "7808aa96-b4a3-4a98-825f-0ca544b39c27",
                "name" => "",
                "amount" => 1,
                "code" => "0026",
                "sum" => 90
            ]
        ];

        try {
            $res = $this->client->request('post', 'orders/checkCreate?' . http_build_query($params), [
                'form_params' => $postParams,
                'http_errors' => false
            ]);
        } catch (Exception $e) {            
            echo (json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE));
            return [];
        }

        return json_decode((string)$res->getBody());
    }

    public function sendOrder(IikoOrder $order)
    {
        if (empty($order->products)) {
            throw new SystemException('Property "products" of class Order can\'t be empty!');
        }

        if (empty($order->customer)) {
            throw new SystemException('Property "customer" of class Order can\'t be empty!');
        }

        if (empty($order->address)) {
            throw new SystemException('Property "address" of class Order can\'t be empty!');
        }

        $params = [
            'access_token' => $this->accessToken,
			'requestTimeout' => "00:02:00"
        ];


        $postParams = [
            'organization' => $this->organization->id,
            'customer' => $order->customer,
            'order' => [
                'id' => '',
                'date' =>  $order->date,
                'phone' => $order->customer->phone,
                'comment' => $order->comment,
                'isSelfService' => $order->isSelfService,
                'personsCount' => $order->personsCount,
                'items' => $order->products,
                'address' => $order->address,
                'paymentItems' => $order->paymentItems
            ]
        ];

        $httpClient = new HttpClient();
        $httpClient->setHeader('Content-Type', 'application/json', true);
        $httpResponse = $httpClient->post(self::IIKO_BASE_URI . 'orders/add?' . http_build_query($params), json_encode($postParams));
        $httpResponseJSON = json_decode($httpResponse);

        return $httpResponseJSON;
    }

    public function prepareProducts($products)
    {
        $productsItems = [];
        foreach ($products as $product) {
            if ($product instanceof Product) {
                $productsItems[] = (array)$product;
            }
        }

        return $productsItems;
    }

    public function getCitiesWithStreets()
    {
        if (is_null($this->organization)) {
            throw new Exception('Для получения групп необходимы данные о организации. Используйте Api->setOrganization(Organization $organization)');
        }

        $orgId = $this->organization->id;

        $params = [
            'access_token' => $this->accessToken,
            'organization' => $orgId
        ];

        try {
            $res = $this->client->request('get', 'cities/cities?' . http_build_query($params));
        } catch (Exception $e) {
            AddMessage2Log($e->getMessage());
            return [];
        }

        AddMessage2Log($res->getBody());
        $json = json_decode((string)$res->getBody());

        //AddMessage2Log($json);
        return $json;
    }

    public static function getIikoBasket(\Bitrix\Sale\Basket $basket)
    {
        $productC = new \stdClass();

        $result = [];

        $basketItems = $basket->getOrderableItems()->getBasketItems();

        foreach ($basketItems as $basketItem) {
			$product = new IikoProduct($productC);
            $product->id = $basketItem->getField('PRODUCT_XML_ID');
            $product->name = $basketItem->getField('NAME');
            $product->amount = $basketItem->getField('QUANTITY');
            $product->sum = $basketItem->getField('PRICE');

            $result[] = $product;
        }

        return $result;
    }

    public function getOrder(IikoOrder $order)
    {
        $orgId = $this->getOrganizationList()[0]->id;
        $params = [
            'access_token' => $this->accessToken,
            'request_timeout' => "00:02:00",
            'organization' => $orgId,
            'order' => $order->id,
        ];
        $httpClient = new HttpClient();
        $httpClient->setHeader('Content-Type', 'application/json', true);
        $httpResponse = $httpClient->get(self::IIKO_BASE_URI . 'orders/info?' . http_build_query($params));
        if ($httpClient->getStatus() === 200) {
            $result = json_decode((string)$httpResponse);
        }
        return $result;
    }


}
