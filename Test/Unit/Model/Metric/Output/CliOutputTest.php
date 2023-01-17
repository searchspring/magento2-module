<?php

namespace SearchSpring\Feed\Test\Unit\Model\Metric\Output;

use SearchSpring\Feed\Model\Metric\Output\CliOutput;
use Symfony\Component\Console\Output\OutputInterface as CliOutputInterface;

class CliOutputTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->cliOutput = new CliOutput();
    }

    public function testPrint()
    {
        $testString = 'test';
        $cliOutputInterface = $this->createMock(CliOutputInterface::class);
        $this->cliOutput->setOutput($cliOutputInterface);
        $cliOutputInterface->expects($this->once())
            ->method('writeln')
            ->with($testString);
        $this->cliOutput->print($testString);
    }
}
