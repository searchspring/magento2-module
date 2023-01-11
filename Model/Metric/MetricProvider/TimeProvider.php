<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric\MetricProvider;

use Magento\Framework\Stdlib\DateTime\DateTime;
use SearchSpring\Feed\Model\Metric\MetricProviderInterface;

class TimeProvider implements MetricProviderInterface
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * TimeProvider constructor.
     * @param DateTime $dateTime
     */
    public function __construct(
        DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
    }

    /**
     * @param array $currentMetrics
     * @param array $previousMetrics
     * @return array
     */
    public function getMetrics(array $currentMetrics, array $previousMetrics): array
    {
        return ['date' => $this->dateTime->gmtDate()];
    }

    /**
     *
     */
    public function reset(): void
    {
        //do nothing
    }
}
