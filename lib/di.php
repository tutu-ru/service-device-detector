<?php

use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use RM\HttpRequestMetadata\RequestMetadataMiddleware;
use RMS\DeviceDetector\Exceptions\HttpExceptionManager;
use RMS\DeviceDetector\Middleware\RequestLogMiddleware;
use RMS\DeviceDetector\Middleware\RequestTimingMiddleware;
use RMS\DeviceDetector\Middleware\SlowRequestLogMiddleware;
use RMS\DeviceDetector\RequestStatsCollector;

return [
    'settings'                       => [
        'displayErrorDetails' => \RMS\DeviceDetector\Config::getShowDetailedErrors()
    ],
    'errorHandler'                   => function (ContainerInterface $container) {
        return $container->get(HttpExceptionManager::class);
    },
    'phpErrorHandler'                => function (ContainerInterface $container) {
        return $container->get(HttpExceptionManager::class);
    },
    'callableResolver'               => function (ContainerInterface $container) {
        return new \Bnf\Slim3Psr15\CallableResolver($container);
    },
    LoggerInterface::class           => function (ContainerInterface $container) {
        return fLog()->createPsrLogger('devicedetector');
    },
    HttpExceptionManager::class      => function (ContainerInterface $container) {
        $exceptionManager = new HttpExceptionManager(fHttpRequestMetadata()->getResponseHandler(), fErrorTracker());
        $exceptionManager->setLogger($container->get(LoggerInterface::class));
        $exceptionManager->setRequestStatsCollector($container->get(RequestStatsCollector::class));
        return $exceptionManager;
    },
    'controller'                     => function (ContainerInterface $container) {
        return new \RMS\DeviceDetector\Controller($container->get(CacheInterface::class));
    },
    RequestStatsCollector::class     => function (ContainerInterface $container) {
        $collector = new RequestStatsCollector($_SERVER['REAL_START_TIME'] ?? null);
        $collector->setLogger($container->get(LoggerInterface::class));
        return $collector;
    },
    RequestTimingMiddleware::class   => function (ContainerInterface $container) {
        return new RequestTimingMiddleware($container->get(RequestStatsCollector::class));
    },
    SlowRequestLogMiddleware::class  => function (ContainerInterface $container) {
        $middleware = new SlowRequestLogMiddleware(0.02);
        $middleware->setLogger($container->get(LoggerInterface::class));
        return $middleware;
    },
    RequestLogMiddleware::class      => function (ContainerInterface $container) {
        $middleware = new RequestLogMiddleware();
        $middleware->setLogger($container->get(LoggerInterface::class));
        return $middleware;
    },
    RequestMetadataMiddleware::class => function (ContainerInterface $container) {
        return new RequestMetadataMiddleware(
            fHttpRequestMetadata()->getRequestHandler(),
            fHttpRequestMetadata()->getResponseHandler()
        );
    },
    CacheInterface::class => function (ContainerInterface $container) {
        return new SimpleCacheBridge(new ApcuCachePool());
    },
];
