<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use SearchSpring\Feed\Api\GetCustomersInterface;
use SearchSpring\Feed\Api\Data\CustomersInterface;
use SearchSpring\Feed\Api\Data\CustomersInterfaceFactory;
use SearchSpring\Feed\Exception\ValidationException;
use SearchSpring\Feed\Helper\Customer;
use SearchSpring\Feed\Helper\Utils;

class GetCustomers implements GetCustomersInterface
{
    /** @var Customer */
    private $helper;

    /** @var CustomersInterfaceFactory */
    private $customersFactory;

    /**
     * @param Customer $helper
     * @param CustomersInterfaceFactory $customersFactory
     */
    public function __construct(Customer $helper, CustomersInterfaceFactory $customersFactory)
    {
        $this->helper = $helper;
        $this->customersFactory = $customersFactory;
    }

    /**
     * @param string $dateRange
     * @param string $rowRange
     *
     * @return CustomersInterface
     *
     * @throws ValidationException
     */
    public function getList(string $dateRange = "All", string $rowRange = "All"): CustomersInterface
    {
        $errors = [];
        if (!Utils::validateDateRange($dateRange)){
            $errors[] = "Invalid date range $dateRange";
        }

        if (!Utils::validateRowRange($rowRange)){
            $errors[] = "Invalid row range $rowRange";
        }

        if (!empty($errors)){
            throw new ValidationException($errors, 400);
        }

        $customers = $this->customersFactory->create();
        $customers->setCustomers($this->helper->getCustomers($dateRange, $rowRange));

        return $customers;
    }
}
