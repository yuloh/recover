<?php

namespace Yuloh\Recover\Renderer\Http;

use Yuloh\Recover\RendererInterface;
use Zend\Diactoros\Response\SapiEmitter;

class DiactorosRenderer implements RendererInterface
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
        (new SapiEmitter())->emit($this->responseBuilder->build($throwable));
    }
}
