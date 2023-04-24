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

use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();
/** @var TaskRepositoryInterface $taskRepository */
$taskRepository = $objectManager->get(TaskRepositoryInterface::class);
$payload = [
    'preSignedUrl' => 'https://testurl.com'
];
/** @var TaskInterface $task */
$task = $objectManager->create(TaskInterface::class);
$task->setPayload($payload)
    ->setType(MetadataInterface::FEED_GENERATION_TASK_CODE)
    ->setStatus(MetadataInterface::TASK_STATUS_SUCCESS);

$taskRepository->save($task);
