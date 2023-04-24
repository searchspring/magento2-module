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

namespace SearchSpring\Feed\Model\Feed\Storage;

class FormatterPool
{
    /**
     * @var array
     */
    private $formatters;

    /**
     * FormatterPool constructor.
     * @param array $formatters
     */
    public function __construct(
        array $formatters = []
    ) {
        $this->formatters = $formatters;
    }

    /**
     * @param string $type
     * @return FormatterInterface|null
     */
    public function get(string $type) : ?FormatterInterface
    {
        return $this->formatters[$type] ?? null;
    }
}
