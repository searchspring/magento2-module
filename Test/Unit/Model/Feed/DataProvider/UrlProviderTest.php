<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\UrlProvider;

class UrlProviderTest extends \PHPUnit\Framework\TestCase
{
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
