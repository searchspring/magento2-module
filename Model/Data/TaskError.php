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

namespace SearchSpring\Feed\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use SearchSpring\Feed\Api\Data\TaskErrorInterface;

class TaskError extends AbstractSimpleObject implements TaskErrorInterface
{
    /**
     * @return int|null
     */
    public function getCode(): ?int
    {
        return !is_null($this->_get(self::CODE))
            ? (int) $this->_get(self::CODE)
            : null;
    }

    /**
     * @param int $code
     * @return TaskErrorInterface
     */
    public function setCode(int $code): TaskErrorInterface
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * @param string $message
     * @return TaskErrorInterface
     */
    public function setMessage(string $message): TaskErrorInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }
}
