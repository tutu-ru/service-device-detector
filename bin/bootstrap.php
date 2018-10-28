<?php

use RM\SoaFramework\Bootstrap\ConfigBootstrapAtom;

define('ROOT_DIR', dirname(__DIR__));
require_once __DIR__ . "/../vendor/autoload.php";

try {
    require_once(__DIR__ . '/../lib/init.php');
    (new ConfigBootstrapAtom())->run('DeviceDetector');

    // TODO: other bootstraps
} catch (\Throwable $e) {
    rm_log_error('DeviceDetector', 'bootstrap', "{$e}");
    fErrorTracker()->send($e);
    exit(1);
}
