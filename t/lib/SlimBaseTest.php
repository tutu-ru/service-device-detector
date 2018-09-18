<?php
namespace RMST\DeviceDetector;

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;

abstract class SlimBaseTest extends Test
{
	protected $_app;


	public function setUp()
	{
		parent::setUp();
		$this->_initApp();
	}


	protected function _initApp()
	{
		// Use the application settings
		$settings = require __DIR__ . '/../../lib/settings.php';
		// initializing $app variable. It'll be used in included files
		$app = new App($settings);
		$this->_app = $app;

		// Set up dependencies
		require __DIR__ . '/../../lib/dependencies.php';

		// Register middleware
		if ($this->withMiddleware)
		{
			require __DIR__ . '/../../lib/middleware.php';
		}

		// Register routes
		require __DIR__ . '/../../lib/routes.php';
	}
	/**
	 * Use middleware when running application?
	 *
	 * @var bool
	 */
	protected $withMiddleware = false;

	/**
	 * Process the application given a request method and URI
	 *
	 * @param string $requestMethod the request method (e.g. GET, POST, etc.)
	 * @param string $requestUri the request URI
	 * @param array|object|null $requestData the request data
	 * @return \Slim\Http\Response
	 */
	protected function _runApp($requestMethod, $requestUri, $requestData = null)
	{
		// Create a mock environment for testing with
		$environment = Environment::mock(
			[
				'REQUEST_METHOD' => $requestMethod,
				'REQUEST_URI'    => $requestUri
			]
		);

		// Set up a request object based on the environment
		$request = Request::createFromEnvironment($environment);

		// Add request data, if it exists
		if (isset($requestData))
		{
			$request = $request->withParsedBody($requestData);
		}

		// Set up a response object
		$response = new Response();

		// Process the application
		$response = $this->_app->process($request, $response);

		// Return the response
		return $response;
	}
}
