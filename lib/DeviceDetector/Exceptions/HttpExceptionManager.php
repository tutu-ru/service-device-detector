<?php
declare(strict_types=1);

namespace RMS\DeviceDetector\Exceptions;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use RM\HttpRequestMetadata\ResponseMetadataHandler;
use RMS\DeviceDetector\RequestStatsCollector;
use Slim\Http\Request;
use Slim\Http\Response;
use RM\ErrorTracker\Facade as ErrorTracker;

class HttpExceptionManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var ResponseMetadataHandler */
    private $responseMetadataHandler;

    /** @var ErrorTracker */
    private $errorTracker;

    /** @var RequestStatsCollector */
    private $requestStatsCollector;

    public function __construct(ResponseMetadataHandler $responseMetadataHandler, ErrorTracker $errorTracker)
    {
        $this->responseMetadataHandler = $responseMetadataHandler;
        $this->errorTracker = $errorTracker;
    }

    public function setRequestStatsCollector(RequestStatsCollector $requestStatsCollector)
    {
        $this->requestStatsCollector = $requestStatsCollector;
    }

    public function __invoke(Request $request, Response $response, \Throwable $exception)
    {
        $this->registerException($exception);

        $data = json_encode(
            [
                'error' => [
                    'message' => $exception->getMessage(),
                    'code'    => $exception->getCode(),
                ]
            ],
            JSON_UNESCAPED_UNICODE
        );
        $response = $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write($data);
        $response = $this->responseMetadataHandler->addToResponse($response);

        if (!is_null($this->requestStatsCollector)) {
            $this->requestStatsCollector->setStatusCode($response->getStatusCode());
            $this->requestStatsCollector->endTiming();
            $this->requestStatsCollector->save();
        }

        return $response;
    }


    private function registerException(\Throwable $exception)
    {
        $this->logger->error(
            "Unhandled exception: {$exception->getMessage()}",
            [
                'code'    => $exception->getCode(),
                'message' => $exception->getMessage(),
                'trace'   => $exception->getTraceAsString(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine()
            ]
        );
        $this->errorTracker->send($exception);
    }
}
