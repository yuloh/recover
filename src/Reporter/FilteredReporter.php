<?php

declare(strict_types=1);

namespace Yuloh\Recover\Reporter;

use Yuloh\Recover\ReporterInterface;

final class FilteredReporter implements ReporterInterface
{
    /**
     * @var string[]
     */
    private $dontReport;

    /**
     * @var ReporterInterface
     */
    private $reporter;

    /**
     * @param \string[]         $dontReport
     * @param ReporterInterface $reporter
     */
    public function __construct(array $dontReport, ReporterInterface $reporter)
    {
        $this->dontReport = $dontReport;
        $this->reporter   = $reporter;
    }

    public function report(\Throwable $throwable): void
    {
        if ($this->shouldReport($throwable)) {
            $this->reporter->report($throwable);
        }
    }

    private function shouldReport(\Throwable $throwable): bool
    {
        foreach ($this->dontReport as $type) {
            if ($throwable instanceof $type) {
                return false;
            }
        }

        return true;
    }
}
