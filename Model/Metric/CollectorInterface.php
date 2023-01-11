<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric;

use SearchSpring\Feed\Model\Metric\View\FormatterInterface;

/**
 * Interface CollectorInterface
 * @package SearchSpring\Feed\Model\Metric
 */
interface CollectorInterface
{
    /**
     *
     */
    const CODE_PRODUCT_FEED = 'product_feed';
    /**
     *
     */
    const PRINT_TYPE_FROM_PREVIOUS = 'from_previous';
    /**
     *
     */
    const PRINT_TYPE_FULL = 'full';

    /**
     * @param string $code
     * @param string|null $title
     * @param array $additionalData
     */
    public function collect(string $code, ?string $title = null, array $additionalData = []) : void;

    /**
     * @param string $code
     * @param string $printType
     */
    public function print(string $code, string $printType = self::PRINT_TYPE_FROM_PREVIOUS) : void;

    /**
     * @param string|null $code
     */
    public function reset(string $code) : void;

    /**
     *
     */
    public function resetAll() : void;

    /**
     * @param string $code
     * @return array
     */
    public function getMetrics(string $code) : array;

    /**
     * @return array
     */
    public function getAllMetrics() : array;

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output) : void;

    /**
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter) : void;
}
