<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric\MetricProvider;

use SearchSpring\Feed\Model\Metric\MetricProviderInterface;

class MemoryProvider implements MetricProviderInterface
{
    /**
     * @param array $currentMetrics
     * @param array $previousMetrics
     * @return array
     */
    public function getMetrics(array $currentMetrics, array $previousMetrics): array
    {
        $result = [
            'usage' => round(memory_get_usage() / 1024 / 1024, 4),
            'usage_real' => round(memory_get_usage(true) / 1024 / 1024, 4),
            'peak' => round(memory_get_peak_usage() / 1024 / 1024, 4),
            'peak_real' => round(memory_get_peak_usage(true) / 1024 / 1024, 4),
        ];

        $previousMetric = array_pop($previousMetrics);
        if (empty($previousMetric)) {
            return $result;
        }

        foreach ($result as $key => $value) {
            if (array_key_exists($key, $previousMetric)) {
                $diff = $value - $previousMetric[$key];
                $diffString = $diff > 0 ? '+' : '-';
                $diffString .= round(abs($diff), 4);
                $result[$key . '_diff'] = $diffString;
            }
        }

        return $result;
    }

    /**
     *
     */
    public function reset(): void
    {
        //do nothing
    }
}
