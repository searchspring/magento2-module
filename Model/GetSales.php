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

use SearchSpring\Feed\Api\GetSalesInterface;
use SearchSpring\Feed\Exception\ValidationException;
use SearchSpring\Feed\Helper\Sale;
use SearchSpring\Feed\Helper\Utils;

class GetSales implements GetSalesInterface
{
    /** @var Sale */
    private $helper;

    /**
     * @param Sale $helper
     */
    public function __construct(Sale $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param string $dateRange
     * @param string $rowRange
     *
     * @return array
     *
     * @throws ValidationException
     */
    public function getList(string $dateRange = "All", string $rowRange = "All")
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

        return $this->helper->getSales($dateRange, $rowRange);
    }
}
