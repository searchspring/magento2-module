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

namespace SearchSpring\Feed\Model\Feed;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class ContextManager implements ContextManagerInterface
{
    /**
     * @var ContextManagerInterface[]
     */
    private $processors;

    /**
     * ContextManager constructor.
     * @param array $processors
     */
    public function __construct(
        array $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     */
    public function setContextFromSpecification(FeedSpecificationInterface $feedSpecification): void
    {
        foreach ($this->processors as $processor) {
            $processor->setContextFromSpecification($feedSpecification);
        }
    }

    /**
     *
     */
    public function resetContext(): void
    {
        foreach ($this->processors as $processor) {
            $processor->resetContext();
        }
    }
}
