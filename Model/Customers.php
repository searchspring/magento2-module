<?php

namespace SearchSpring\Feed\Model;

use SearchSpring\Feed\Api\Data\CustomersDataInterface;
use SearchSpring\Feed\Api\Data\CustomersInterface;

class Customers implements CustomersInterface
{
    private $customers;

    public function __construct()
    {
    }

    /**
     * @return CustomersDataInterface[]
     */
    public function getCustomers(): array
    {
        return $this->customers;
    }

    /**
     * @param $value CustomersDataInterface[]
     */
    public function setCustomers(array $value)
    {
        $this->customers = $value;
    }
}