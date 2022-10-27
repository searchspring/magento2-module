<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\Collection\StockModifier;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockModifierTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var StockModifier
     */
    private $stockModifier;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->stockModifier = $this->objectManager->get(StockModifier::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_oos.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_not_manage_stock.php
     */
    public function testModify() : void
    {
        $specification = $this->specificationBuilder->build(['includeOutOfStock' => true]);
        $collection = $this->getCollection();
        $this->stockModifier->modify($collection, $specification);
        $this->assertTrue($collection->getFlag('has_stock_status_filter'));
        $skus = [];
        foreach ($collection as $item) {
            $skus[] = $item->getSku();
        }

        $this->assertTrue(in_array('searchspring_simple_oos', $skus));
        $this->assertTrue(in_array('searchspring_simple_not_manage_stock', $skus));
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_oos.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_not_manage_stock.php
     */
    public function testModifyWithExcludeOutOfStockProducts() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $collection = $this->getCollection();
        $this->stockModifier->modify($collection, $specification);
        $this->assertTrue($collection->getFlag('has_stock_status_filter'));
        $skus = [];
        foreach ($collection as $item) {
            $skus[] = $item->getSku();
        }

        $this->assertFalse(in_array('searchspring_simple_oos', $skus));
        $this->assertTrue(in_array('searchspring_simple_not_manage_stock', $skus));
    }

    /**
     * @return Collection
     */
    private function getCollection() : Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
