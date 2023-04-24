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

namespace SearchSpring\Feed\Model\ResourceModel\Task\Error;

use Magento\Framework\App\ResourceConnection;
use SearchSpring\Feed\Model\ResourceModel\Task;

class DeleteErrors
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * DeleteErrors constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param array $taskIds
     */
    public function execute(array $taskIds) : void
    {
        $taskIds = array_map('intval', $taskIds);
        $this->resourceConnection->getConnection()->delete(Task::ERROR_TABLE, ['task_id IN (?)' => $taskIds]);
    }
}
