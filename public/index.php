<?php

define('ROOT_DIR', dirname(__DIR__));
require_once __DIR__ . "/../vendor/autoload.php";
$traceTitle = fTools()->web()->getRequestPath();

try
{
	require_once(__DIR__ . '/../lib/init.php');
	fXRequestId()->init();

	echo "Съешь ещё этих мягких французских булок, да выпей же чаю";

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
