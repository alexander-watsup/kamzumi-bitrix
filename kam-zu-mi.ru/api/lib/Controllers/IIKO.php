<?php

namespace KamZuMi\Api\Controllers;

use Slim\Routing\RouteContext;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;


class IIKO
{

    public function getStreets(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $cityId = $route->getArgument('cityId');

        $iiko = new \Reklamafia\Exchange\IIKO();
        $streets = $iiko->getStreets($cityId);

        $result = ['error' => 0, 'data' => $streets];
        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function checkOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                die(); // !!! Achtung                
            }
        }

        //print_r($contents);

        $params = [
            'date' => $contents->date,
            'order' => [
                'items' => $contents['order']['items'],
                "phone" => $contents['order']['phone'],
                "customerName" => $contents['order']['customerName'],
                "address" => [
                    'city' => $contents['order']['address']['city'],
                    'street' => $contents['order']['address']['street'],
                    'home' => $contents['order']['address']['home']
                ]
            ]
        ];

        //print_r($params);

        $iiko = new \Reklamafia\Exchange\IIKO();
        $checkResult = $iiko->checkOrder($params);


        $result = ['error' => 0, 'data' => $checkResult];
        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
