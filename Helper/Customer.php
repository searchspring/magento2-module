<?php
/**
 * Helper to fetch customer data.
 *
 * This file is part of SearchSpring/Feed.
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace SearchSpring\Feed\Helper;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;

class Customer extends AbstractHelper
{
    protected $customerFactory;

    public function __construct(CollectionFactory $customerFactory)
    {
        $this->customerFactory = $customerFactory;
    }

    public function getCustomers(string $dateRangeStr, string $rowRangeStr)
    {
        $result = [];
        $customerCollection = $this->customerFactory->create();

        $select = $customerCollection->getSelect();

        // Build date range query.
        $dateRange = Utils::getDateRange($dateRangeStr);
        if ($dateRange) {
            $customerCollection->addBindParam(':from', $dateRange[0]);
            $condition = '(e.created_at >= :from OR e.updated_at >= :from)';

            if (isset($dateRange[1])) {
                $plusOneDay = Utils::plusOneDay($dateRange[1], 'Y-m-d');
                $customerCollection->addBindParam(':to', $plusOneDay);
                $condition = <<<SQL
                    (
                        (e.created_at >= :from AND e.created_at <= :to) 
                        OR 
                        (e.updated_at >= :from AND e.updated_at <= :to)
                    )
                SQL;
            }

            $select->where($condition);
        }

        // Chunk customers with row range.
        $rowRange = Utils::getRowRange($rowRangeStr);
        if (isset($rowRange[0]) && isset($rowRange[1])) {
            $select->limit((int)$rowRange[1], (int)$rowRange[0]);
        }

        $items = $customerCollection->getItems(); // Make query
        foreach ($items as $item) {
            $result[] = [
                'id' => $item->getId(),
                'email' => $item->getEmail()
            ];
        }

        return $result;
    }
}
