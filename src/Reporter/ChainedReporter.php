<?php

declare(strict_types=1);

namespace Yuloh\Recover\Reporter;

use Yuloh\Recover\ReporterInterface;

final class ChainedReporter implements ReporterInterface
{
    /**
     * @var ReporterInterface[]
     */
    private $reporters;

    public function __construct(ReporterInterface ...$reporters)
    {
        $this->reporters = $reporters;
    }

    public function report(\Throwable $throwable): void
    {
        foreach ($this->reporters as $reporter) {
            $reporter->report($throwable);
        }
    }
}
