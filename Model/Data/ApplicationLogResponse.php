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

use Magento\Framework\DataObject;
use SearchSpring\Feed\Api\Data\ApplicationLogResponseInterface;

class ApplicationLogResponse extends DataObject implements ApplicationLogResponseInterface
{
    private const LINES = 'lines';
    private const CONTENT = 'content';
    private const COMPRESSED = 'compressed';

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        return $this->getData(self::LINES) ?? [];
    }

    /**
     * @param string[] $lines
     * @return $this
     */
    public function setLines(array $lines)
    {
        return $this->setData(self::LINES, $lines);
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @return bool
     */
    public function getCompressed(): bool
    {
        return (bool) $this->getData(self::COMPRESSED);
    }

    /**
     * @param bool $compressed
     * @return $this
     */
    public function setCompressed(bool $compressed)
    {
        return $this->setData(self::COMPRESSED, $compressed);
    }
}
