<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get(
    '/openapi.json',
    function (Request $request, Response $response, $args) {
        $jsonResponse = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
        $jsonResponse->getBody()
            ->write(file_get_contents(ROOT_DIR . '/api/v1/openapi/index.json'));
        return $jsonResponse;
    }
);
$app->get(
    '/device_info/',
    function (Request $request, Response $response, $args) {
        return $this->get('controller')->getDeviceInfo($request, $response, $args);
    }
)->setName('getDeviceInfo');
