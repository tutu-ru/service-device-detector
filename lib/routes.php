<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

use Slim\Http\Request;
use Slim\Http\Response;


// Routes
$app->get(
	'/device_info/',
	function(Request $request, Response $response, $args)
	{
		return $this->controller->getDeviceInfo($request, $response, $args);
	}
)->setName('getDeviceInfo');