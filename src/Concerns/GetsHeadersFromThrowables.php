<?php

namespace Yuloh\Recover\Concerns;

trait GetsHeadersFromThrowables
{
    protected function getHeaders(\Throwable $throwable): array
    {
        if (method_exists($throwable, 'getHeaders')) {
            try {
                return $throwable->getHeaders();
            } catch (\Throwable $_) {
                return [];
            }
        }

        return [];
    }
}
