<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric;

interface MetricProviderInterface
{
    /**
     * @param array $currentMetrics
     * @param array $previousMetrics
     * @return array
     */
    public function getMetrics(array $currentMetrics, array $previousMetrics) : array;

    /**
     *
     */
    public function reset() : void;
}
