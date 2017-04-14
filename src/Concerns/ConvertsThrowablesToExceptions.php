<?php

declare(strict_types=1);

namespace Yuloh\Recover\Concerns;

trait ConvertsThrowablesToExceptions
{
    protected function convertThrowableToException(\Throwable $exception): \Exception
    {
        if (!$exception instanceof \Exception) {
            $exception = new \ErrorException(
                $exception->getMessage(),
                $exception->getCode(),
                1,
                $exception->getFile(),
                $exception->getLine(),
                $exception->getPrevious()
            );
        }

        return $exception;
    }
}
