<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\SaleableProvider;

class SaleableProviderTest extends \PHPUnit\Framework\TestCase
{
    private $saleableProvider;

    public function setUp(): void
    {
        $this->saleableProvider = new SaleableProvider();
    }

    public function testGetData()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $productMock = $this->createMock(Product::class);
        $products = [
            [
                'product_model' => $productMock
            ]
        ];
        $productMock->expects($this->once())
            ->method('isSaleable')
            ->willReturn(true);

        $this->assertSame(
            [
                [
                    'product_model' => $productMock,
                    'saleable' => true
                ]
            ],
            $this->saleableProvider->getData($products, $feedSpecificationMock)
        );
    }
}
