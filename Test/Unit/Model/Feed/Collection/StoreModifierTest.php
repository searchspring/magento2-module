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

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\StoreModifier;

class StoreModifierTest extends \PHPUnit\Framework\TestCase
{
    private $storeModifier;

    public function setUp(): void
    {
        $this->storeModifier = new StoreModifier();
    }

    public function testModify()
    {
        $storeCode = 'default';
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getStoreCode')
            ->willReturn($storeCode);
        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('setStore')
            ->with($storeCode)
            ->willReturnSelf();

        $this->assertSame($collectionMock, $this->storeModifier->modify($collectionMock, $feedSpecificationMock));
    }
}
