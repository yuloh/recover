<?php

declare(strict_types=1);

namespace Yuloh\Recover\Renderer\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Yuloh\Recover\Concerns\ConvertsThrowablesToExceptions;
use Yuloh\Recover\RendererInterface;

class SymfonyApplicationRenderer implements RendererInterface
{
    use ConvertsThrowablesToExceptions;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param Application          $application
     * @param OutputInterface|null $output
     */
    public function __construct(Application $application, OutputInterface $output = null)
    {
        $this->application = $application;
        $this->output      = $output;
    }

    public function render(\Throwable $throwable): void
    {
        $this->application->renderException(
            $this->convertThrowableToException($throwable),
            $this->output ?: new ConsoleOutput()
        );
    }
}
