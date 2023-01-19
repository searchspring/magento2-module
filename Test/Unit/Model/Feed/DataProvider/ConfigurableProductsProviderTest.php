<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\ConfigurableProductsProvider;
use SearchSpring\Feed\Model\Feed\DataProvider\Product\GetChildProductsData;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\DataProvider;

class ConfigurableProductsProviderTest extends \PHPUnit\Framework\TestCase
{
    private $getChildProductsDataMock;

    private $providerMock;

    private $configurableProductsProvider;

    public function setUp(): void
    {
        $this->getChildProductsDataMock = $this->createMock(GetChildProductsData::class);
        $this->providerMock = $this->createMock(DataProvider::class);
        $this->configurableProductsProvider = new ConfigurableProductsProvider(
            $this->getChildProductsDataMock,
            $this->providerMock
        );
    }

    public function testGetData()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $productMock = $this->createMock(Product::class);
        $childProductMock = $this->createMock(Product::class);
        $products = [
            [
                'product_model' => $productMock
            ]
        ];
        $configurableProducts = [
            1 => $productMock
        ];
        $childProducts = [
            1 => [$childProductMock]
        ];
        $childProductsData = ['child_sku' => 'test'];
        $this->providerMock->expects($this->once())
            ->method('getConfigurableProducts')
            ->with($products)
            ->willReturn($configurableProducts);
        $this->providerMock->expects($this->once())
            ->method('getAllChildProducts')
            ->with($products, $feedSpecificationMock)
            ->willReturn($childProducts);
        $this->providerMock->expects($this->once())
            ->method('getConfigurableAttributes')
            ->with($configurableProducts, $feedSpecificationMock)
            ->willReturn([1 => ['attribute_code']]);
        $this->providerMock->expects($this->once())
            ->method('getLinkField')
            ->willReturn('entity_id');
        $productMock->expects($this->once())
            ->method('getData')
            ->with('entity_id')
            ->willReturn(1);
        $this->getChildProductsDataMock->expects($this->once())
            ->method('getProductData')
            ->with($products[0], [$childProductMock], ['attribute_code'], $feedSpecificationMock)
            ->willReturn($childProductsData);

        $this->assertSame(
            [array_merge($products[0], $childProductsData)],
            $this->configurableProductsProvider->getData($products, $feedSpecificationMock)
        );
    }
}
