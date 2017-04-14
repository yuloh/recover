<?php

declare(strict_types=1);

namespace Yuloh\Recover\Reporter;

use Yuloh\Recover\ReporterInterface;

final class CallbackReporter implements ReporterInterface
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function report(\Throwable $throwable): void
    {
        ($this->callback)($throwable);
    }
}
