<?php
/** @noinspection PhpUnhandledExceptionInspection */

use RM\ServiceConfig\ConfigBuilder;
use RMS\DeviceDetector\SharedMemoryCachePsrBridge;

(new ConfigBuilder())
    ->setTempDir('tmp/')
    ->setApplicationConfigFile(__DIR__ . '/../config/application.xml')
    ->setApplicationConfigCache('/tmp/config.cache')
    ->setEtcdCache(new SharedMemoryCachePsrBridge("config"), null)
    ->init('DeviceDetector');
