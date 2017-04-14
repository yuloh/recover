<?php

declare(strict_types=1);

namespace Yuloh\Recover;

interface ReporterInterface
{
    public function report(\Throwable $throwable): void;
}
