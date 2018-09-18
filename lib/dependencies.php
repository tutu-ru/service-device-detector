<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

// DIC configuration
$container = $app->getContainer();
$container['controller'] = new \RMS\DeviceDetector\Controller();
