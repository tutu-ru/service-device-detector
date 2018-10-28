<?php
/**
 * @author Seleznyov Artyom seleznev@tutu.ru
 */

namespace RMS\DeviceDetector\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use RMS\DeviceDetector\RequestStatsCollector;

class RequestTiming
{
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $statsCollector = new RequestStatsCollector($request->getUri()->getPath(), $request->getMethod());
        $statsCollector->startTiming();

        /** @var Response $response */
        $response = $next($request, $response);

        $statsCollector->endTiming();
        $statsCollector->setStatusCode($response->getStatusCode());
        $statsCollector->save();

        return $response;
    }
}
