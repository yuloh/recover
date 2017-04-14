<?php

declare(strict_types=1);

namespace Yuloh\Recover;

final class ThrowableSerializer
{
    public function serialize(\Throwable $throwable): array
    {
        return [
            'message'  => $throwable->getMessage(),
            'class'    => get_class($throwable),
            'code'     => $throwable->getCode(),
            'file'     => $throwable->getFile(),
            'line'     => $throwable->getLine(),
            'trace'    => $this->serializeTrace($throwable->getTrace()),
            'previous' => is_null($throwable->getPrevious()) ? null : $this->serialize($throwable->getPrevious()),
        ];
    }

    private function serializeTrace(array $trace): array
    {
        return array_map(function (array $entry) {
            if (isset($entry['args'])) {
                $entry['args'] = $this->serializeArgs($entry['args']);
            }
            return $entry;
        }, $trace);
    }

    private function serializeArgs(array $args, $depth = 0): array
    {
        return array_map(function ($v) use ($depth) {
            switch (true) {
                case is_object($v):
                    return ['object', get_class($v)];
                case is_array($v):
                    return [
                        'array',
                        $depth > 10 ? '<ARRAY>' : $this->serializeArgs($v, $depth +1)
                    ];
                case is_bool($v):
                    return ['boolean', $v];
                case is_int($v):
                    return ['integer', $v];
                case is_float($v):
                    return ['float', $v];
                case is_resource($v);
                    return ['resource', get_resource_type($v)];
                case is_null($v):
                    return ['null', null];
                default:
                    return ['string', $v];
            }
        }, $args);
    }
}
