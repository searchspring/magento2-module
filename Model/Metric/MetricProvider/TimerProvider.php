<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric\MetricProvider;

use Magento\Framework\Stdlib\DateTime\DateTime;
use SearchSpring\Feed\Model\Metric\MetricProviderInterface;

class TimerProvider implements MetricProviderInterface
{
    /**
     * @var DateTime
     */
    private $dateTime;

    private $startTime = null;

    private $previousTimer = '00 00:00:00';

    /**
     * TimerProvider constructor.
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
     * @throws \Exception
     */
    public function getMetrics(array $currentMetrics, array $previousMetrics): array
    {
        $currentDateString = $this->dateTime->gmtDate();
        if (!$this->startTime) {
            $this->startTime = $currentDateString;
            return ['timer' => $this->previousTimer];
        }

        $startDate = new \DateTimeImmutable($this->startTime);
        $currentDate = new \DateTimeImmutable($currentDateString);
        $startCurrentInterval = $startDate->diff($currentDate);
        $currentTimerString = $startCurrentInterval->format("%a %H:%I:%S");
        $result['timer'] = $currentTimerString;
        return $result;
//        $previousTimer = new \DateTimeImmutable('2022-01-' . $this->previousTimer);
//        $currentTimer = new \DateTimeImmutable('2022-01-' . $currentTimerString);
//        $previousCurrentInterval = $previousTimer->diff($currentTimer);
//        $currentTimerDiff = $previousCurrentInterval->format("%a:%H:%I:%S");
//        $timerData = explode(':', $currentTimerDiff);
//        $this->previousTimer = $timerData[0] . ' ' . $timerData[1] . ':' . $timerData[2] . ':' . $timerData[3];
//        $timerDiffString = '+';
//        $writeNextTimeParts = false;
//        if ($timerData[0] != '0') {
//            $timerDiffString .= $timerData[0] . 'd ';
//            $writeNextTimeParts = true;
//        }
//
//        if ($timerData[1] != '00' || $writeNextTimeParts) {
//            $timerDiffString .= $timerData[1] . 'h ';
//            $writeNextTimeParts = true;
//        }
//
//        if ($timerData[2] != '00' || $writeNextTimeParts) {
//            $timerDiffString .= $timerData[2] . 'm ';
//            $writeNextTimeParts = true;
//        }
//
//        if ($timerData[3] != '00' || $writeNextTimeParts) {
//            $timerDiffString .= $timerData[3] . 's ';
//        }
//
//        $result['timer_diff'] = $timerDiffString;
//        return $result;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->startTime = null;
        $this->previousTimer = '00 00:00:00';
    }
}
