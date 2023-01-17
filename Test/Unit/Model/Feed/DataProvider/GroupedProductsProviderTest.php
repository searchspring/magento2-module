<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\GroupedProduct\Model\Product\Type\Grouped as Grouped;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ChildAttributesProvider;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ValueProcessor;
use SearchSpring\Feed\Model\Feed\DataProvider\Grouped\GetChildCollection;
use SearchSpring\Feed\Model\Feed\DataProvider\GroupedProductsProvider;
use SearchSpring\Feed\Model\Feed\DataProvider\Product\GetChildProductsData;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class GroupedProductsProviderTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->childAttributesProviderMock = $this->createMock(ChildAttributesProvider::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->getChildCollectionMock = $this->createMock(GetChildCollection::class);
        $this->metadataPoolMock = $this->createMock(MetadataPool::class);
        $this->getChildProductsDataMock = $this->createMock(GetChildProductsData::class);
        $this->valueProcessorMock = $this->createMock(ValueProcessor::class);
        $this->groupedProductsProvider = new GroupedProductsProvider(
            $this->childAttributesProviderMock,
            $this->storeManagerMock,
            $this->getChildCollectionMock,
            $this->metadataPoolMock,
            $this->getChildProductsDataMock,
            $this->valueProcessorMock
        );
    }

    public function testGetData()
    {
        $collectionMock = $this->createMock(Collection::class);
        $storeMock = $this->createMock(Store::class);
        $attributeMock = $this->createMock(Attribute::class);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $entityMetadataMock = $this->getMockForAbstractClass(EntityMetadataInterface::class);
        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $childProductMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $products = [
            [
                'product_model' => $productMock
            ]
        ];
        $childProducts = [
            1 => $childProductMock
        ];
        $childProductsData = ['child_sku' => 'test'];
        $this->metadataPoolMock->expects($this->any())
            ->method('getMetadata')
            ->with(ProductInterface::class)
            ->willReturn($entityMetadataMock);
        $entityMetadataMock->expects($this->any())
            ->method('getLinkField')
            ->willReturn('entity_id');
        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn(Grouped::TYPE_CODE);
        $productMock->expects($this->any())
            ->method('getData')
            ->willReturn(1);
        $childProductMock->expects($this->any())
            ->method('getData')
            ->willReturn(1);
        $this->childAttributesProviderMock->expects($this->once())
            ->method('getAttributes')
            ->with($feedSpecificationMock)
            ->willReturn([$attributeMock]);

        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn('code');

        $feedSpecificationMock->expects($this->once())
            ->method('getStoreCode')
            ->willReturn('1');
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with('1')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn('1');

        $this->getChildCollectionMock->expects($this->once())
            ->method('execute')
            ->withAnyParameters()
            ->willReturn($collectionMock);
        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($childProducts);
        $this->getChildProductsDataMock->expects($this->once())
            ->method('getProductData')
            ->with($products[0], [$childProductMock], [$attributeMock], $feedSpecificationMock)
            ->willReturn($childProductsData);

        $this->assertSame(
            [array_merge($products[0], $childProductsData)],
            $this->groupedProductsProvider->getData($products, $feedSpecificationMock)
        );
    }
}
