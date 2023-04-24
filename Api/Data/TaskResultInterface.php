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

namespace SearchSpring\Feed\Api\Data;

interface TaskResultInterface
{
    const TASK = 'task';
    const RESULT = 'result';
    /**
     * @return \SearchSpring\Feed\Api\Data\TaskInterface|null
     */
    public function getTask() : ?TaskInterface;

    /**
     * @param \SearchSpring\Feed\Api\Data\TaskInterface $task
     * @return TaskResultInterface
     */
    public function setTask(TaskInterface $task) : self;

    /**
     * @return mixed
     */
    public function getResult();

    /**
     * @param $result
     * @return mixed
     */
    public function setResult($result);
}
