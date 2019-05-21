<?php

$_SERVER['REAL_START_TIME'] = microtime(true);
define('ROOT_DIR', dirname(__DIR__));
require_once __DIR__ . "/../vendor/autoload.php";

try {
    require_once(__DIR__ . '/../lib/init.php');

    $di  = require __DIR__ . '/../lib/di.php';
    $app = new \Slim\App($di);

    // Register middleware
    require __DIR__ . '/../lib/middleware.php';
    // Register routes
    require __DIR__ . '/../lib/routes.php';
    $app->run();
} catch (\Throwable $e) {
    fLog()->saveThroughNativeErrorLog($e->__toString(), 'php://stderr', true, "   _/|\_   ");
    fErrorTracker()->send($e);
    http_response_code(500);
} finally {
    fStatsD()->sendAll();
}
