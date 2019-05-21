<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

/**
 * Middlewares выполняются начиная с последнего.
 * Последний middleware в этом файле будет выполнен первым.
 */

use RM\HttpRequestMetadata\RequestMetadataMiddleware;
use RMS\DeviceDetector\Config;
use RMS\DeviceDetector\Middleware\RequestLogMiddleware;
use RMS\DeviceDetector\Middleware\RequestTimingMiddleware;
use RMS\DeviceDetector\Middleware\SlowRequestLogMiddleware;

$container = $app->getContainer();

$app->add($container->get(RequestMetadataMiddleware::class));
$app->add($container->get(SlowRequestLogMiddleware::class));
if (Config::isRequestLogEnabled()) {
    $app->add($container->get(RequestLogMiddleware::class));
}
/**
 * Этот middleware должен оставаться последним в файле, т.к. здесь идет замер времени выполнения запроса,
 * а middlewares выполняются от последнего к первому
 */
$app->add($container->get(RequestTimingMiddleware::class));
