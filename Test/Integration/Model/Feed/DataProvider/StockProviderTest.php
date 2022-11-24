<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\DataProvider\StockProvider;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockProviderTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;
    /**
     * @var GetProducts
     */
    private $getProducts;
    /**
     * @var StockProvider
     */
    private $stockProvider;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        $this->getProducts = $this->objectManager->get(GetProducts::class);
        $this->stockProvider = $this->objectManager->get(StockProvider::class);
        parent::setUp();
    }

    /**
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoConfigFixture current_store cataloginventory/options/show_out_of_stock 1
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_oos.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products_oos_simples.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/grouped_products.php
     *
     * @throws \Exception
     */
    public function testGetData() : void
    {
        $specification = $this->specificationBuilder->build(['includeOutOfStock' => true]);
        $products = $this->getProducts->get($specification);
        $data = $this->stockProvider->getData($products, $specification);
        $config = [
            'searchspring_simple_1' => ['in_stock' => 1, 'stock_qty' => 100],
            'searchspring_simple_2' => ['in_stock' => 1, 'stock_qty' => 100],
            'searchspring_simple_oos' => ['in_stock' => 0, 'stock_qty' => 100],
            'searchspring_configurable_test_configurable' => ['in_stock' => 1, 'stock_qty' => 0],
            'searchspring_configurable_test_configurable_2_attributes' => ['in_stock' => 1, 'stock_qty' => 0],
            'searchspring_configurable_test_oos_simple_configurable' => ['in_stock' => 0, 'stock_qty' => 0],
            'searchspring_grouped_test_simple_1000' => ['in_stock' => 1, 'stock_qty' => 100],
            'searchspring_grouped_test_simple_1001' => ['in_stock' => 1, 'stock_qty' => 100],
            'searchspring_grouped_test_simple_1010' => ['in_stock' => 1, 'stock_qty' => 100],
            'searchspring_grouped_test_simple_1011' => ['in_stock' => 1, 'stock_qty' => 100],
            'searchspring_grouped_test_simple_1012' => ['in_stock' => 1, 'stock_qty' => 100],
            'searchspring_grouped_test_simple_1013' => ['in_stock' => 1, 'stock_qty' => 100],
            'searchspring_grouped_test_grouped_1' => ['in_stock' => 1, 'stock_qty' => 0],
            'searchspring_grouped_test_grouped_2' => ['in_stock' => 1, 'stock_qty' => 0],
        ];
        $this->assertStock($data, $config);
        $this->stockProvider->reset();
    }

    /**
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     *
     * @throws \Exception
     */
    public function testReset() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $products = $this->getProducts->get($specification);
        $this->stockProvider->getData($products, $specification);
        $this->stockProvider->reset();
        $this->assertTrue(true);
    }

    /**
     * @param array $items
     * @param array $config
     */
    private function assertStock(array $items, array $config) : void
    {
        foreach ($items as $item) {
            /** @var Product $product */
            $product = $item['product_model'] ?? null;
            if (!$product) {
                continue;
            }

            $sku = $product->getSku();
            $productConfig = $config[$sku] ?? [];
            foreach ($productConfig as $key => $value) {
                if (!is_null($value)) {
                    $this->assertArrayHasKey($key, $item, 'sku: ' . $sku . ';key: ' . $key);
                    $this->assertEquals($value, $item[$key], 'sku: ' . $sku . ';key: ' . $key);
                } else {
                    $this->assertArrayNotHasKey($key, $item, 'sku: ' . $sku . ';key: ' . $key);
                }
            }
        }
    }
}
