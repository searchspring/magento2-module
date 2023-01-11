<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric\MetricProvider;

use SearchSpring\Feed\Model\Metric\MetricProviderInterface;

class FileSizeProvider implements MetricProviderInterface
{
    private $lastSize = null;
    /**
     * @param array $currentMetrics
     * @param array $previousMetrics
     * @return array
     */
    public function getMetrics(array $currentMetrics, array $previousMetrics): array
    {
        $size = array_key_exists('size', $currentMetrics) ? (int) $currentMetrics['size'] : null;
        if (is_null($size)) {
            return [];
        }

        $result = ['size_readable' => $this->formatSize($size)];
        if (is_null($this->lastSize)) {
            $this->lastSize = $size;
            return $result;
        }

        $sizeDiff = $size - $this->lastSize;
        $result['size_diff'] = $sizeDiff;
        $sizeDiffReadable = $sizeDiff > 0 ? '+' : '-';
        $sizeDiffReadable .= $this->formatSize(abs($sizeDiff));
        $result['size_diff_readable'] = $sizeDiffReadable;
        $this->lastSize = $size;
        return $result;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->lastSize = null;
    }

    /**
     * @param int $size
     * @return string
     */
    private function formatSize(int $size) : string
    {
        $suffixes = ['B', 'KB', 'MB', 'GB'];
        $result = '';
        foreach ($suffixes as $suffix) {
            $newSize = $size / 1024;
            if ($newSize < 1) {
                $result = round($size, 2) . $suffix;
                break;
            }

            $size = $newSize;
        }

        return $result;
    }
}
