<?php
declare(strict_types=1);

namespace RMS\DeviceDetector\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RMS\DeviceDetector\RequestStatsCollector;

class RequestTimingMiddleware implements MiddlewareInterface
{
    /** @var RequestStatsCollector */
    private $requestStatsCollector;

    public function __construct(RequestStatsCollector $requestStatsCollector)
    {
        $this->requestStatsCollector = $requestStatsCollector;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->requestStatsCollector->startTiming();
        $this->requestStatsCollector->setRequest($request);

        $response = $handler->handle($request);

        $this->requestStatsCollector->setStatusCode($response->getStatusCode());
        $this->requestStatsCollector->endTiming();
        $this->requestStatsCollector->save();

        return $response;
    }
}
