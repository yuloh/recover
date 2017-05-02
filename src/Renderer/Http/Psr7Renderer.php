<?php

namespace Yuloh\Recover\Renderer\Http;

use Yuloh\Recover\RendererInterface;

class Psr7Renderer implements RendererInterface
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
        \Http\Response\send($this->responseBuilder->build($throwable));
    }
}
