<?php

namespace SearchSpring\Feed\Test\Unit\Model\Metric\MetricProvider;

use SearchSpring\Feed\Model\Metric\MetricProvider\FileSizeProvider;

class FileSizeProviderTest extends \PHPUnit\Framework\TestCase
{
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
