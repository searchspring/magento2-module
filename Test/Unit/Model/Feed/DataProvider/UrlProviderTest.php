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

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\UrlProvider;

class UrlProviderTest extends \PHPUnit\Framework\TestCase
{
    private $urlProvider;

    public function setUp(): void
    {
        $this->urlProvider = new UrlProvider();
    }

    public function testGetData()
    {
        $productUrl = 'test.url';
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $productMock = $this->createMock(Product::class);
        $products = [
            [
                'product_model' => $productMock
            ]
        ];
        $productMock->expects($this->once())
            ->method('getProductUrl')
            ->willReturn($productUrl);

        $this->assertSame(
            [
                [
                    'product_model' => $productMock,
                    'url' => $productUrl
                ]
            ],
            $this->urlProvider->getData($products, $feedSpecificationMock)
        );
    }
}
