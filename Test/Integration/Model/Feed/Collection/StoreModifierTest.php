<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Collection;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\Collection\StoreModifier;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreModifierTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var StoreModifier
     */
    private $storeModifier;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->storeModifier = $this->objectManager->get(StoreModifier::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products_multistore.php
     */
    public function testModify() : void
    {
        $storeCode = 'fixturestore';
        $specification = $this->specificationBuilder->build(['store' => $storeCode]);
        $collection = $this->getCollection();
        $this->storeModifier->modify($collection, $specification);
        // add name attribute to filter to check if we load a specific store data in collection
        $collection->addAttributeToSelect('name');
        $store = $this->objectManager->create(Store::class);
        $store->load($storeCode, 'code');
        $storeId = $store->getId();
        $this->assertNotNull($storeId);
        $this->assertEquals((int) $storeId, $collection->getStoreId());
        $item = $collection->getFirstItem();
        $sku = $item->getSku();
        $this->assertEquals('StoreTitle', $item->getName());
        $product = $this->productRepository->get($sku);
        // check that for default store same product have a different name
        $this->assertNotEquals('StoreTitle', $product->getName());
    }

    /**
     * @return Collection
     */
    private function getCollection() : Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
