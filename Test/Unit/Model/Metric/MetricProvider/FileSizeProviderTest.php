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

use SearchSpring\Feed\Model\Metric\MetricProvider\FileSizeProvider;

class FileSizeProviderTest extends \PHPUnit\Framework\TestCase
{
    private $fileSizeProvider;

    public function setUp(): void
    {
        $this->fileSizeProvider = new FileSizeProvider();
    }

    public function testGetMetrics()
    {
        $currentMetricsTestData = [
            'size' => 1024000
        ];

        $this->assertSame(
            ['size_readable' => '1000KB'],
            $this->fileSizeProvider->getMetrics($currentMetricsTestData, [])
        );
    }
}
