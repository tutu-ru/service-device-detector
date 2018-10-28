<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

namespace RMS\DeviceDetector;

use RM\StatsD\DataCollector;
use RM\StatsD\Metric;

class RequestStatsCollector extends DataCollector
{
    private $_uri;
    private $_method;
    private $_statusCode;

    public function __construct($uri, $method)
    {
        $this->_uri = str_replace('/', '_', preg_replace('#(^/|/$)#', '', $uri));
        $this->_method = strtolower($method);
    }

    public function setStatusCode($statusCode)
    {
        $this->_statusCode = $statusCode;
    }

    protected function _getTimingKey()
    {
        $keyParts = [
            Metric::TYPE_BUSINESS,
            'devicedetector',
            'api_requests',
            $this->_uri,
            $this->_method,
            $this->_statusCode ?? 'unknown'
        ];

        return $this->_glueNamespaces($keyParts);
    }

    protected function _saveCustomMetrics()
    {
    }

    protected function _getSession()
    {
        return fStatsD()->getGarbageSession();
    }
}
