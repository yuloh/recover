<?php

declare(strict_types=1);

namespace Yuloh\Recover\Renderer\Console;

use Yuloh\Recover\RendererInterface;
use Yuloh\Recover\ThrowableSerializer;

final class EchoRenderer implements RendererInterface
{
    /**
     * @var bool
     */
    private $verbose;

    /**
     * @param bool $verbose
     */
    public function __construct(bool $verbose = true)
    {
        $this->verbose = $verbose;
    }

    public function render(\Throwable $throwable): void
    {
        $msg = sprintf(
            "\u{26A0}  %s : %s  \u{26A0} %s",
            get_class($throwable),
            $throwable->getMessage(),
            PHP_EOL
        );

        if ($this->verbose) {
            $msg .= json_encode(
                    (new ThrowableSerializer())->serialize($throwable),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                ) . PHP_EOL;
        }

        echo $msg;
    }
}
