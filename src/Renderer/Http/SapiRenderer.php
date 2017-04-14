<?php

namespace Yuloh\Recover\Renderer\Http;

use Yuloh\Recover\RendererInterface;

class SapiRenderer implements RendererInterface
{
    /**
     * @var ResponseBuilderInterface
     */
    private $responseBuilder;

    public function __construct(ResponseBuilderInterface $responseBuilder)
    {
        $this->responseBuilder = $responseBuilder;
    }

    public function render(\Throwable $throwable): void
    {
        $response = $this->responseBuilder->build($throwable);

        if (!headers_sent()) {
            header(sprintf('HTTP/1.0 %s', $response->getStatusCode()));
            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
        }

        ob_end_clean();

        echo $response->getBody()->__toString();
    }
}
