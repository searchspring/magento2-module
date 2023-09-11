<?php

namespace SearchSpring\Feed\Api\Data;

interface CustomersInterface
{
    /**
     * @return \SearchSpring\Feed\Api\Data\CustomersDataInterface[]
     */
    public function getCustomers(): array;

    /**
     * @param $value \SearchSpring\Feed\Api\Data\CustomersDataInterface[]
     * @return null
     */
    public function setCustomers(array $value);
}