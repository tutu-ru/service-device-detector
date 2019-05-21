<?php

use RM\ServiceConfig\ConfigBootstrap;

define('ROOT_DIR', dirname(__DIR__));
require_once __DIR__ . "/../vendor/autoload.php";

try {
    (new ConfigBootstrap(__DIR__ . '/../bootstrap'))->run('DeviceDetector');

    require_once(__DIR__ . '/../lib/init.php');
    // TODO: other bootstraps
} catch (\Throwable $e) {
    fLog()->saveThroughNativeErrorLog($e->__toString(), 'php://stderr', true, "   _/|\_   ");
    exit(1);
}
