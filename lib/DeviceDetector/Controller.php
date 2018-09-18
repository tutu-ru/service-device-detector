<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

namespace RMS\DeviceDetector;

use DeviceDetector\Parser\OperatingSystem;
use \Slim\Http\Request;
use \Slim\Http\Response;

class Controller
{
	private const _DEVICE_DETECTOR_NS = 'device_detector';

	/**
	 * @var \DeviceDetector\DeviceDetector[]
	 */
	private $_engine = [];

	/**
	 * @var FileCache
	 */
	private $_cache;

	/**
	 * @param Request  $request
	 * @param Response $response
	 *
	 * @return \Slim\Http\Response
	 */
	public function getDeviceInfo(Request $request, Response $response, array $args)
	{
		$requestParams = $request->getParams();
		if (!isset($requestParams['userAgent']) || !$requestParams['userAgent'])
			return $response->withStatus(400, 'Bad request');

		$userAgent = $requestParams['userAgent'];
		$deviceInfo = $this->_getDeviceInfoFromCache($userAgent);
		if (is_null($deviceInfo))
		{
			$deviceInfo = $this->_getDeviceInfo($userAgent);
			$this->_setDeviceInfoToCache($userAgent, $deviceInfo);
		}

		return $response->withJson($deviceInfo);
	}

	private function _getDeviceInfoFromCache(string $userAgent): ?array
	{
		return rm_shared_memory_cache_get(self::_DEVICE_DETECTOR_NS, $userAgent);
	}

	private function _setDeviceInfoToCache(string $userAgent, array $deviceInfo)
	{
		rm_shared_memory_cache_set(
			self::_DEVICE_DETECTOR_NS,
			$userAgent,
			$deviceInfo,
			Config::getSharedMemoryTtl()
		);
	}


	protected function _getDeviceInfo($userAgent): array
	{
		$engine = $this->_getEngine($userAgent);
		$engineWithSkipBotDetection = $this->_getEngine($userAgent, true);
		$osData = $engine->getOs();

		$result = [
			'is_mobile' => $engineWithSkipBotDetection->isMobile(),
			'is_tablet' => $engineWithSkipBotDetection->isTablet(),
			'is_bot' => $engine->isBot(),
			'os_name' => OperatingSystem::getOsFamily($osData['short_name']),
			'os_version' => $osData['version']
		];

		return $result;
	}

	/**
	 * @param string $userAgent
	 * @param bool $skipBotDetection
	 *
	 * @return \DeviceDetector\DeviceDetector
	 */
	protected function _getEngine($userAgent, $skipBotDetection = false)
	{
		if (!isset($this->_engine[$skipBotDetection]))
		{
			$detector = new \DeviceDetector\DeviceDetector($userAgent);
			$detector->skipBotDetection($skipBotDetection);
			$detector->setCache($this->_getCache());
			$detector->parse();
			$this->_engine[$skipBotDetection] = $detector;
		}

		return $this->_engine[$skipBotDetection];
	}

	/**
	 * @return FileCache
	 */
	protected function _getCache()
	{
		if (is_null($this->_cache))
			$this->_cache = new FileCache();

		return $this->_cache;
	}
}