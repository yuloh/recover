<?php

declare(strict_types=1);

namespace Yuloh\Recover\Reporter;

use Yuloh\Recover\Concerns\ConvertsThrowablesToExceptions;
use Yuloh\Recover\ReporterInterface;

final class RavenReporter implements ReporterInterface
{
    use ConvertsThrowablesToExceptions;

    /**
     * @var \Raven_Client
     */
    private $ravenClient;

    /**
     * @param \Raven_Client $ravenClient
     */
    public function __construct(\Raven_Client $ravenClient)
    {
        $this->ravenClient = $ravenClient;
    }

    public function report(\Throwable $throwable): void
    {
        $this->ravenClient->captureException($this->convertThrowableToException($throwable));
    }
}
