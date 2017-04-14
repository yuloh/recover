<?php

namespace Yuloh\Recover\Concerns;

trait GetsStatusCodesFromThrowables
{
    protected function getStatusCode(\Throwable $throwable): int
    {
        if (method_exists($throwable, 'getStatusCode')) {
            try {
                return (int) $throwable->getStatusCode();
            } catch (\Throwable $_) {
                return 500;
            }
        }

        if (method_exists($throwable, 'getHttpStatusCode')) {
            try {
                return (int) $throwable->getHttpStatusCode();
            } catch (\Throwable $_) {
                return 500;
            }
        }

        return 500;
    }
}
