<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

namespace RMS\DeviceDetector;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use RM\StatsD\DataCollector;
use RM\StatsD\Metric;

class RequestStatsCollector extends DataCollector implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var float */
    private $realStartTime;

    /** @var float */
    private $prepareTime;

    private $uri;
    private $method;
    private $statusCode;

    public function __construct(float $realStartTime = null)
    {
        $this->realStartTime = $realStartTime;
    }


    public function startTiming($timeSeconds = null)
    {
        if (!is_null($this->realStartTime)) {
            $this->prepareTime = microtime(true) - $this->realStartTime;
        }
        parent::startTiming($timeSeconds ?? $this->realStartTime);
    }


    public function setRequest(ServerRequestInterface $request)
    {
        $this->uri = str_replace('/', '_', preg_replace('#(^/|/$)#', '', $request->getUri()->getPath()));
        $this->method = strtolower($request->getMethod());
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    protected function _getTimingKey()
    {
        return $this->getKey(['response', $this->statusCode ?? 'unknown']);
    }


    private function getKey(array $parts)
    {
        $baseParts = [
            Metric::TYPE_LOW_LEVEL,
            'http_service',
            'devicedetector',
            'api_request',
            $this->uri,
            $this->method,
        ];
        return $this->_glueNamespaces(array_merge($baseParts, $parts));
    }

    protected function _saveCustomMetrics()
    {
        if (!is_null($this->prepareTime)) {
            $this->_getSession()->timing($this->getKey(['prepare']), $this->prepareTime);
        }
    }
}
