<?php

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
