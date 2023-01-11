<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric\View;

interface FormatterInterface
{
    /**
     * @param array $data
     * @param string $code
     * @return string
     */
    public function format(array $data, string $code) : string;
}
