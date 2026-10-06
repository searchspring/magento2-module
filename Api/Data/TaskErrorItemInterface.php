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

namespace SearchSpring\Feed\Api\Data;

interface TaskErrorItemInterface
{
    const TASK_ID = 'task_id';
    const CODE = 'code';
    const MESSAGE = 'message';
    const TASK_TYPE = 'task_type';
    const TASK_STATUS = 'task_status';
    const TASK_CREATED_AT = 'task_created_at';
    const TASK_STARTED_AT = 'task_started_at';
    const TASK_ENDED_AT = 'task_ended_at';

    /**
     * @return int
     */
    public function getTaskId(): int;

    /**
     * @param int $taskId
     * @return $this
     */
    public function setTaskId(int $taskId);

    /**
     * @return int
     */
    public function getCode(): int;

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code);

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message);

    /**
     * @return string|null
     */
    public function getTaskType(): ?string;

    /**
     * @param string|null $type
     * @return $this
     */
    public function setTaskType(?string $type);

    /**
     * @return string|null
     */
    public function getTaskStatus(): ?string;

    /**
     * @param string|null $status
     * @return $this
     */
    public function setTaskStatus(?string $status);

    /**
     * @return string|null
     */
    public function getTaskCreatedAt(): ?string;

    /**
     * @param string|null $date
     * @return $this
     */
    public function setTaskCreatedAt(?string $date);

    /**
     * @return string|null
     */
    public function getTaskStartedAt(): ?string;

    /**
     * @param string|null $date
     * @return $this
     */
    public function setTaskStartedAt(?string $date);

    /**
     * @return string|null
     */
    public function getTaskEndedAt(): ?string;

    /**
     * @param string|null $date
     * @return $this
     */
    public function setTaskEndedAt(?string $date);
}
