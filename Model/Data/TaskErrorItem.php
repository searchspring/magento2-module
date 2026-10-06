<?php
/**
 * Copyright (C) 2026 Searchspring <https://searchspring.com>
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

namespace SearchSpring\Feed\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use SearchSpring\Feed\Api\Data\TaskErrorItemInterface;

class TaskErrorItem extends AbstractSimpleObject implements TaskErrorItemInterface
{
    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return (int) $this->_get(self::TASK_ID);
    }

    /**
     * @param int $taskId
     * @return $this
     */
    public function setTaskId(int $taskId)
    {
        return $this->setData(self::TASK_ID, $taskId);
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return (int) $this->_get(self::CODE);
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return (string) $this->_get(self::MESSAGE);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @return string|null
     */
    public function getTaskType(): ?string
    {
        return $this->_get(self::TASK_TYPE);
    }

    /**
     * @param string|null $type
     * @return $this
     */
    public function setTaskType(?string $type)
    {
        return $this->setData(self::TASK_TYPE, $type);
    }

    /**
     * @return string|null
     */
    public function getTaskStatus(): ?string
    {
        return $this->_get(self::TASK_STATUS);
    }

    /**
     * @param string|null $status
     * @return $this
     */
    public function setTaskStatus(?string $status)
    {
        return $this->setData(self::TASK_STATUS, $status);
    }

    /**
     * @return string|null
     */
    public function getTaskCreatedAt(): ?string
    {
        return $this->_get(self::TASK_CREATED_AT);
    }

    /**
     * @param string|null $date
     * @return $this
     */
    public function setTaskCreatedAt(?string $date)
    {
        return $this->setData(self::TASK_CREATED_AT, $date);
    }

    /**
     * @return string|null
     */
    public function getTaskStartedAt(): ?string
    {
        return $this->_get(self::TASK_STARTED_AT);
    }

    /**
     * @param string|null $date
     * @return $this
     */
    public function setTaskStartedAt(?string $date)
    {
        return $this->setData(self::TASK_STARTED_AT, $date);
    }

    /**
     * @return string|null
     */
    public function getTaskEndedAt(): ?string
    {
        return $this->_get(self::TASK_ENDED_AT);
    }

    /**
     * @param string|null $date
     * @return $this
     */
    public function setTaskEndedAt(?string $date)
    {
        return $this->setData(self::TASK_ENDED_AT, $date);
    }
}
