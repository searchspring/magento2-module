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

namespace SearchSpring\Feed\Api;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Exception\UniqueTaskException;
use SearchSpring\Feed\Exception\ValidationException;

interface CreateTaskInterface
{
    /**
     * @param string $type
     * @param mixed $payload
     * @return TaskInterface
     * @throws CouldNotSaveException
     * @throws ValidationException
     * @throws UniqueTaskException
     * @throws Exception
     */
    public function execute(string $type, $payload) : TaskInterface;
}
