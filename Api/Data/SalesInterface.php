<?php

namespace SearchSpring\Feed\Api\Data;

interface SalesInterface
{
    /**
     * @return \SearchSpring\Feed\Api\Data\SalesDataInterface[]
     */
    public function getSales(): array;

    /**
     * @param $value \SearchSpring\Feed\Api\Data\SalesDataInterface[]
     * @return null
     */
    public function setSales(array $value);
}