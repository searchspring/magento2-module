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

interface ApplicationLogResponseInterface
{
    /**
     * Log lines, filled when the output is not compressed.
     *
     * @return string[]
     */
    public function getLines(): array;

    /**
     * @param string[] $lines
     * @return $this
     */
    public function setLines(array $lines);

    /**
     * Compressed log content (base64url of gzdeflate), filled when the output is compressed.
     *
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * @param string|null $content
     * @return $this
     */
    public function setContent(?string $content);

    /**
     * @return bool
     */
    public function getCompressed(): bool;

    /**
     * @param bool $compressed
     * @return $this
     */
    public function setCompressed(bool $compressed);
}
