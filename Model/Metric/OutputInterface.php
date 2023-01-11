<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric;

interface OutputInterface
{
    /**
     * @param string $data
     */
    public function print(string $data) : void;
}
