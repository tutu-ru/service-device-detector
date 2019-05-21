<?php
declare(strict_types=1);

namespace RMS\DeviceDetector\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class SlowRequestLogMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;


    /** @var float */
    private $maxAllowedTimeSec;


    public function __construct(float $maxAllowedTimeSec)
    {
        $this->maxAllowedTimeSec = $maxAllowedTimeSec;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startTime = microtime(true);

        $response = $handler->handle($request);

        $resultTime = microtime(true) - $startTime;
        if ($resultTime > $this->maxAllowedTimeSec) {
            $this->logger->warning(
                "Slow request {$request->getUri()->getPath()}: {$resultTime}",
                [
                    'time'    => $resultTime,
                    'maxTime' => $this->maxAllowedTimeSec,
                    'uri'     => $request->getUri()->getPath(),
                    'method'  => $request->getMethod(),
                ]
            );
        }

        return $response;
    }
}
