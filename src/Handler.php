<?php

declare(strict_types=1);

namespace Yuloh\Recover;

use Yuloh\Recover\Reporter\NullReporter;

final class Handler
{
    private const FATAL_ERRORS = E_ERROR | E_USER_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_PARSE;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var ReporterInterface
     */
    private $reporter;

    /**
     * @param RendererInterface $renderer
     * @param ReporterInterface $reporter
     */
    public function __construct(RendererInterface $renderer, ReporterInterface $reporter = null)
    {
        $this->renderer = $renderer;
        $this->reporter = $reporter ?: new NullReporter();
    }

    public function install(): void
    {
        error_reporting(-1);

        set_error_handler(function (int $level, string $message, string $file, int $line) {
            if (error_reporting() & $level) {
                throw new \ErrorException($message, 0, $level, $file, $line);
            }
        });

        set_exception_handler(function (\Throwable $throwable) {
            $this->handleException($throwable);
        });

        register_shutdown_function(function () {
            $this->onShutdown();
        });
    }

    public function handleException(\Throwable $throwable): void
    {
        $this
            ->report($throwable)
            ->render($throwable);
    }

    public function report(\Throwable $throwable): Handler
    {
       $this->reporter->report($throwable);

       return $this;
    }

    public function render(\Throwable $throwable): Handler
    {
        $this->renderer->render($throwable);

        return $this;
    }

    private function onShutdown(): void
    {
        if (is_null($error = error_get_last()) || !($error & self::FATAL_ERRORS)) {
            return;
        }
        $this->handleException(
            new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line'])
        );
    }
}
