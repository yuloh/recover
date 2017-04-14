<?php

declare(strict_types=1);

namespace Yuloh\Recover\Renderer\Http;

use Psr\Http\Message\ResponseInterface;

interface ResponseBuilderInterface
{
    public function build(\Throwable $throwable): ResponseInterface;
}
