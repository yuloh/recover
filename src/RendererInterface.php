<?php

declare(strict_types=1);

namespace Yuloh\Recover;

interface RendererInterface
{
    public function render(\Throwable $throwable): void;
}
