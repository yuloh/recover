<?php

declare(strict_types=1);

namespace Yuloh\Recover\Reporter;

use Yuloh\Recover\ReporterInterface;

final class NullReporter implements ReporterInterface
{
    public function report(\Throwable $throwable): void
    {
        return;
    }
}
