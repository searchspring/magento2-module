<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SearchSpring\Feed\Test\Unit\Model\Metric\Output;

use SearchSpring\Feed\Model\Metric\Output\CliOutput;
use Symfony\Component\Console\Output\OutputInterface as CliOutputInterface;

class CliOutputTest extends \PHPUnit\Framework\TestCase
{
    private $cliOutput;

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
