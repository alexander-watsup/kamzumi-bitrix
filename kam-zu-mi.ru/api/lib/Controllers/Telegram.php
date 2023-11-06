<?php

namespace KamZuMi\Api\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Telegram
{


    public function sendMessage(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                die(); // !!! Achtung                
            }
        }

        if (!isset($contents['message'])) {
            \Reklamafia\Messaging\Telegram::sendMessage("Wrong request (1)");
        }

        $message = mb_substr($contents['message'], 0, 1000);
        \Reklamafia\Messaging\Telegram::sendMessage($message);

        $result = ['error' => 0];
        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_UNICODE));

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
