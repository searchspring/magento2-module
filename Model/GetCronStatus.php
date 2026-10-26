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

use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as ScheduleCollectionFactory;
use Magento\Framework\Exception\InputException;
use SearchSpring\Feed\Api\GetCronStatusInterface;

class GetCronStatus implements GetCronStatusInterface
{
    public const MAX_PAGE_SIZE = 200;

    /**
     * @var ScheduleCollectionFactory
     */
    private $scheduleCollectionFactory;

    /**
     * @param ScheduleCollectionFactory $scheduleCollectionFactory
     */
    public function __construct(
        ScheduleCollectionFactory $scheduleCollectionFactory
    ) {
        $this->scheduleCollectionFactory = $scheduleCollectionFactory;
    }

    /**
     * Get cron status list for searchspring_task_execution
     *
     * @param string $status
     * @param int $currentPage
     * @param int $pageSize
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws InputException
     */
    public function getList(
        string $status = '',
        int $currentPage = 1,
        int $pageSize = 20,
        string $startDate = '',
        string $endDate = ''
    ): array {
        $currentPage = max(1, $currentPage);
        $pageSize = min(max(1, $pageSize), self::MAX_PAGE_SIZE);
        $collection = $this->scheduleCollectionFactory->create();
        
        // Filter by searchspring job code
        $collection->addFieldToFilter('job_code', 'searchspring_task_execution');
        
        // Apply status filter if provided
        if (!empty($status)) {
            $collection->addFieldToFilter('status', $status);
        }
        
        // cron_schedule dates are stored in UTC
        if ($startDate !== '') {
            $collection->addFieldToFilter('scheduled_at', ['gteq' => $this->formatDate($startDate, 'startDate')]);
        }
        if ($endDate !== '') {
            $endDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate) ? $endDate . ' 23:59:59' : $endDate;
            $collection->addFieldToFilter('scheduled_at', ['lteq' => $this->formatDate($endDate, 'endDate')]);
        }

        // Default ordering - latest first
        $collection->setOrder('scheduled_at', 'DESC');
        
        // Get total count before applying pagination
        $totalCount = $collection->getSize();
        
        // Apply pagination
        $collection->setPageSize($pageSize);
        $collection->setCurPage($currentPage);
        
        $cronStatusItems = [];
        /** @var \Magento\Cron\Model\Schedule $scheduleItem */
        foreach ($collection->getItems() as $scheduleItem) {
            $cronStatusItems[] = [
                'schedule_id' => (int)$scheduleItem->getScheduleId(),
                'job_code' => $scheduleItem->getJobCode(),
                'status' => $scheduleItem->getStatus(),
                'messages' => $scheduleItem->getMessages() ?: '',
                'created_at' => $scheduleItem->getCreatedAt(),
                'scheduled_at' => $scheduleItem->getScheduledAt(),
                'executed_at' => $scheduleItem->getExecutedAt() ?: '',
                'finished_at' => $scheduleItem->getFinishedAt() ?: ''
            ];
        }
        
        return [
            'data' => [
                'cron_jobs' => $cronStatusItems,
                'total' => $totalCount,
                'currentPage' => $currentPage,
                'pageSize' => $pageSize
            ]
        ];
    }

    /**
     * @param string $date
     * @param string $fieldName
     * @return string
     * @throws InputException
     */
    private function formatDate(string $date, string $fieldName): string
    {
        $timestamp = strtotime($date . (preg_match('/(Z|[+-]\d{2}:?\d{2}|UTC|GMT)$/i', $date) ? '' : ' UTC'));
        if ($timestamp === false) {
            throw new InputException(__('Invalid %1 "%2", use e.g. 2026-01-01 or 2026-01-01T10:00:00', $fieldName, $date));
        }

        return gmdate('Y-m-d H:i:s', $timestamp);
    }
}
