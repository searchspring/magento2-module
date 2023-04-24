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

namespace SearchSpring\Feed\Test\Unit\Model\Metric\MetricProvider;

use Magento\Framework\Stdlib\DateTime\DateTime;
use SearchSpring\Feed\Model\Metric\MetricProvider\TimerProvider;

class TimerProviderTest extends \PHPUnit\Framework\TestCase
{
    private $dateTimeMock;

    private $timerProvider;

    public function setUp(): void
    {
        $this->dateTimeMock = $this->createMock(DateTime::class);
        $this->timerProvider = new TimerProvider($this->dateTimeMock);
    }

    public function testGetMetrics()
    {
        $currentTime = '1999-12-31 23:59:59';
        $this->dateTimeMock->expects($this->once())
            ->method('gmtDate')
            ->willReturn($currentTime);

        $this->assertSame(['timer' => '00 00:00:00'], $this->timerProvider->getMetrics([], []));
    }
}
