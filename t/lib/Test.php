<?php

namespace RMT\DeviceDetector;

abstract class Test extends \RM_Test_More
{
	public function setUp()
	{
		parent::setUp();
		fConfig()->initWithLibraries([ROOT_DIR . '/t/config.xml']);
		ftConfig()->setTempDir('t/tmp');
	}


	public function tearDown()
	{
		parent::tearDown();
	}
}
