<?php

declare(strict_types=1);

namespace Yuloh\Recover\Renderer\Http\ResponseBuilder;

use Interop\Http\Factory\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yuloh\Recover\Concerns\GetsHeadersFromThrowables;
use Yuloh\Recover\Concerns\GetsStatusCodesFromThrowables;
use Yuloh\Recover\Renderer\Http\ResponseBuilderInterface;
use Yuloh\Recover\ThrowableSerializer;

final class JsonResponseBuilder implements ResponseBuilderInterface
{
    use GetsStatusCodesFromThrowables,
        GetsHeadersFromThrowables;

    private const DEFAULT_HEADERS = ['Content-Type' => 'application/json'];

    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var int
     */
    private $options;

    public function __construct(ResponseFactoryInterface $responseFactory, bool $debug = false, int $options = JSON_PARTIAL_OUTPUT_ON_ERROR)
    {
        $this->responseFactory = $responseFactory;
        $this->options         = $options;
        $this->debug           = $debug;
    }

    public function build(\Throwable $throwable): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($this->getStatusCode($throwable));

        foreach (array_merge(self::DEFAULT_HEADERS, $this->getHeaders($throwable)) as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        $response->getBody()->write($this->getContent($throwable));

        return $response;
    }

    private function getContent($throwable)
    {
        if (($statusCode = $this->getStatusCode($throwable)) === 404) {
            $message   = 'Not found';
        } else {
            $message = 'Internal server error';
        }

        $content = [
            'error' => [
                'message' => $message,
                'code'    => $statusCode
            ]
        ];

        if ($this->debug) {
           $content['error']['data'] = (new ThrowableSerializer())->serialize($throwable);
        }
        return json_encode($content, $this->options);
    }
}
