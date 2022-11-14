<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\GroupedProductsProvider;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GroupedProductsProviderTest extends TestCase
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
     * @var GroupedProductsProvider
     */
    private $groupedProductsProvider;
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;
    /**
     * @var AssertChildProducts
     */
    private $assertChildProducts;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        $this->getProducts = $this->objectManager->get(GetProducts::class);
        $this->groupedProductsProvider = $this->objectManager->get(GroupedProductsProvider::class);
        $this->contextManager = $this->objectManager->get(ContextManagerInterface::class);
        $this->assertChildProducts = $this->objectManager->get(AssertChildProducts::class);
        parent::setUp();
    }
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/grouped_products.php
     *
     * @throws \Exception
     */
    public function testGetData() : void
    {
        $specification = $this->specificationBuilder->build(['includeChildPrices' => true]);
        $products = $this->getProducts->get($specification);
        $data = $this->groupedProductsProvider->getData($products, $specification);
        $config = [
            'products' => [
                'searchspring_grouped_test_grouped_1' => [
                    'child_count' => 2,
                    'sku_prefix' => 'searchspring_grouped_test_simple_',
                    'name_prefix' => 'SearchSpring Grouped Test Simple',
                ],
                'searchspring_grouped_test_grouped_2' => [
                    'child_count' => 4,
                    'sku_prefix' => 'searchspring_grouped_test_simple_',
                    'name_prefix' => 'SearchSpring Grouped 2 Test Simple'
                ]
            ],
            'required_attributes' => ['child_sku', 'child_sku', 'child_final_price']
        ];

        $this->assertChildProducts->assertChildProducts($data, $config);
        $this->groupedProductsProvider->reset();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture Magento/Catalog/_files/product_boolean_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_decimal_attribute.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/grouped_products.php
     *
     * @throws \Exception
     */
    public function testGetDataWithAdditionalAttributes() : void
    {
        $specification = $this->specificationBuilder->build([
            'includeChildPrices' => true,
            'childFields' => ['boolean_attribute', 'decimal_attribute']
        ]);
        $products = $this->getProducts->get($specification);
        $data = $this->groupedProductsProvider->getData($products, $specification);
        $config = [
            'products' => [
                'searchspring_grouped_test_grouped_1' => [
                    'child_count' => 2,
                    'sku_prefix' => 'searchspring_grouped_test_simple_',
                    'name_prefix' => 'SearchSpring Grouped Test Simple',
                    'value_map' => [
                        'decimal_attribute' => ['1000.000000', '1001.000000'],
                        'boolean_attribute' => ['Yes', 'Yes']
                    ]
                ],
                'searchspring_grouped_test_grouped_2' => [
                    'child_count' => 4,
                    'sku_prefix' => 'searchspring_grouped_test_simple_',
                    'name_prefix' => 'SearchSpring Grouped 2 Test Simple',
                    'value_map' => [
                        'decimal_attribute' => ['1010.000000', '1011.000000', '1012.000000', '1013.000000'],
                        'boolean_attribute' => ['No', 'No', 'No', 'No']
                    ]
                ]
            ],
            'required_attributes' => ['child_sku', 'child_name', 'child_final_price'],
            'additional_attributes' => ['boolean_attribute', 'decimal_attribute']
        ];

        $this->assertChildProducts->assertChildProducts($data, $config);
        $this->groupedProductsProvider->reset();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/grouped_products_with_store_value.php
     *
     * @throws \Exception
     */
    public function testGetDataWithMultistoreValues() : void
    {
        $specification = $this->specificationBuilder->build(['includeChildPrices' => true,]);
        $products = $this->getProducts->get($specification);
        $data = $this->groupedProductsProvider->getData($products, $specification);
        $config = [
            'products' => [
                'searchspring_grouped_test_grouped_1' => [
                    'child_count' => 2,
                    'sku_prefix' => 'searchspring_grouped_test_simple_',
                    'name_prefix' => 'Store Default SearchSpring Grouped Test Simple'
                ],
                'searchspring_grouped_test_grouped_2' => [
                    'child_count' => 4,
                    'sku_prefix' => 'searchspring_grouped_test_simple_',
                    'name_prefix' => 'Store Default SearchSpring Grouped 2 Test Simple'
                ]
            ],
            'required_attributes' => ['child_sku', 'child_name', 'child_final_price']
        ];

        $this->assertChildProducts->assertChildProducts($data, $config);
        $this->groupedProductsProvider->reset();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/grouped_products_disabled_simple.php
     *
     * @throws \Exception
     */
    public function testGetDataWithDisabledSimples() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $products = $this->getProducts->get($specification);
        $data = $this->groupedProductsProvider->getData($products, $specification);
        foreach ($data as $product) {
            $this->assertArrayNotHasKey('child_sku', $product);
            $this->assertArrayNotHasKey('child_name', $product);
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/grouped_products.php
     *
     * @throws \Exception
     */
    public function testReset() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $products = $this->getProducts->get($specification);
        $this->groupedProductsProvider->getData($products, $specification);
        $this->groupedProductsProvider->reset();
        $this->assertTrue(true);
    }
}
