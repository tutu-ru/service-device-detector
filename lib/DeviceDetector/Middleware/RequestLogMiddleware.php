<?php
declare(strict_types=1);

namespace RMS\DeviceDetector\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class RequestLogMiddleware implements MiddlewareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger->info(
            'request',
            [
                'method' => $request->getMethod(),
                'path'   => $request->getUri()->getPath(),
                'params' => $this->getRequestParams($request)
            ]
        );

        $response = $handler->handle($request);

        $response->getBody()->rewind();
        $payload = json_decode($response->getBody()->getContents());

        $this->logger->info(
            'response',
            [
                'statusCode' => $response->getStatusCode(),
                'contents'   => $payload
            ]
        );

        return $response;
    }


    private function getRequestParams(ServerRequestInterface $request)
    {
        $requestParams = $request->getQueryParams();
        $postParams = $request->getParsedBody();
        if ($postParams) {
            $requestParams = array_replace($requestParams, (array)$postParams);
        }

        foreach ($requestParams as $paramName => $paramValue) {
            if ($paramName == '_url') {
                unset($requestParams[$paramName]);
            }
        }

        return $requestParams;
    }
}