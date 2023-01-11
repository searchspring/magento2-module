<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric\Output;

use SearchSpring\Feed\Model\Metric\OutputInterface;
use Symfony\Component\Console\Output\OutputInterface as CliOutputInterface;

class CliOutput implements OutputInterface
{
    /**
     * @var CliOutputInterface|null
     */
    private $output;

    /**
     * @param CliOutputInterface $output
     */
    public function setOutput(CliOutputInterface $output) : void
    {
        $this->output = $output;
    }

    /**
     * @param string $data
     * @throws \Exception
     */
    public function print(string $data): void
    {
        if (!$this->output) {
            throw new \Exception('output is not defined');
        }

        $this->output->writeln($data);
    }
}
