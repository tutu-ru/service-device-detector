<?php

namespace RMST\DeviceDetector;

abstract class Test extends \RM_Test_More
{
	public function setUp()
	{
		parent::setUp();
		fConfig()->initServiceMode('devicedetector');
		ftConfig()->initWithLibraries([ROOT_DIR . '/t/config.xml']);
		ftConfig()->setTempDir('t/tmp');
	}
}
