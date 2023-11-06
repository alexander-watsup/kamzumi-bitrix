<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require __DIR__ . '/../vendor/autoload.php';


$app = AppFactory::create();
$app->setBasePath("/api/v1");

$app->get('/iiko/city/{cityId}/streets', KamZuMi\Api\Controllers\IIKO::class . ':getStreets');
$app->post('/iiko/delivery/checkOrder', KamZuMi\Api\Controllers\IIKO::class . ':checkOrder');
$app->post('/orders/createOrder', KamZuMi\Api\Controllers\Orders::class . ':createOrder');
$app->post('/telegram/send', KamZuMi\Api\Controllers\Telegram::class . ':sendMessage');

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Kam-Zu-Mi");
    return $response;
});

$app->run();
