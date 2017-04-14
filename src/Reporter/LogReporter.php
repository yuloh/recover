<?php

declare(strict_types=1);

namespace Yuloh\Recover\Reporter;

use Psr\Log\LoggerInterface;
use Yuloh\Recover\ReporterInterface;

final class LogReporter implements ReporterInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function report(\Throwable $throwable): void
    {
        $this->logger->error($throwable->getMessage(), compact('throwable'));
    }
}
