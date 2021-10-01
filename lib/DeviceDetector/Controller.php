<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

namespace RMS\DeviceDetector;

use DeviceDetector\Cache\PSR16Bridge;
use DeviceDetector\Parser\OperatingSystem;
use Psr\SimpleCache\CacheInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Controller
{
    private const _DEVICE_DETECTOR_NS = 'device_detector_';

    /**
     * @var \DeviceDetector\DeviceDetector[]
     */
    private $engine = [];

    /** @var CacheInterface */
    private $cache;


    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return \Slim\Http\Response
     */
    public function getDeviceInfo(Request $request, Response $response, array $args)
    {
        $requestParams = $request->getParams();
        if (!isset($requestParams['userAgent']) || strlen($requestParams['userAgent']) === 0) {
            return $response->withStatus(400, 'Bad request');
        }

        $userAgent = $requestParams['userAgent'];
        $deviceInfo = $this->getDeviceInfoFromCache($userAgent);
        if (is_null($deviceInfo)) {
            $deviceInfo = $this->loadDeviceInfo($userAgent);
            $this->setDeviceInfoToCache($userAgent, $deviceInfo);
        }

        return $response->withJson($deviceInfo);
    }

    private function getDeviceInfoFromCache(string $userAgent): ?array
    {
        return $this->cache->get($this->getCacheKey($userAgent));
    }

    private function setDeviceInfoToCache(string $userAgent, array $deviceInfo)
    {
        $this->cache->set($this->getCacheKey($userAgent), $deviceInfo, Config::getSharedMemoryTtl());
    }

    private function getCacheKey(string $userAgent): string
    {
        return self::_DEVICE_DETECTOR_NS . str_replace(['{', '}', '(', ')', '/', '\\', '@', ':'], '_', $userAgent);
    }


    private function loadDeviceInfo($userAgent): array
    {
        $engine = $this->getEngine($userAgent);
        $engineWithSkipBotDetection = $this->getEngine($userAgent, true);
        $osData = $engine->getOs();

        $osName = OperatingSystem::getOsFamily((string) $osData['short_name']);
        if ($osName == null) {
            $osName = false;
        }

        $result = [
            'is_mobile' => $engineWithSkipBotDetection->isMobile(),
            'is_tablet' => $engineWithSkipBotDetection->isTablet(),
            'is_bot' => $engine->isBot(),
            'os_name' => $osName,
            'os_version' => $osData['version']
        ];

        return $result;
    }

    /**
     * @param string $userAgent
     * @param bool   $skipBotDetection
     *
     * @return \DeviceDetector\DeviceDetector
     */
    protected function getEngine($userAgent, $skipBotDetection = false)
    {
        if (!isset($this->engine[$skipBotDetection])) {
            $detector = new \DeviceDetector\DeviceDetector($userAgent);
            $detector->skipBotDetection($skipBotDetection);
            $detector->setCache(new PSR16Bridge($this->cache));
            $detector->parse();
            $this->engine[$skipBotDetection] = $detector;
        }

        return $this->engine[$skipBotDetection];
    }
}
