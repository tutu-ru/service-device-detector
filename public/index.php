<?php

define('ROOT_DIR', dirname(__DIR__));
require_once __DIR__ . "/../vendor/autoload.php";
$traceTitle = fTools()->web()->getRequestPath();

try
{
	require_once(__DIR__ . '/../lib/init.php');
	fXRequestId()->init();

	$settings = require __DIR__ . '/../lib/settings.php';
	$app = new \Slim\App($settings);

	// Set up dependencies
	require __DIR__ . '/../lib/dependencies.php';
	// Register middleware
	//require __DIR__ . '/../lib/middleware.php';
	// Register routes
	require __DIR__ . '/../lib/routes.php';
	$app->run();

	if (!is_null($traceTitle))
		fOpenTracing()->in($traceTitle);
}
catch (\Throwable $e)
{
	rm_log_error('DeviceDetector', 'runtime', "{$e}");
	fErrorTracker()->send($e);
}
finally
{
	if (!is_null($traceTitle))
	{
		fOpenTracing()->out($traceTitle);
		fOpenTracing()->send();
	}
	fStatsD()->sendAll();
}
