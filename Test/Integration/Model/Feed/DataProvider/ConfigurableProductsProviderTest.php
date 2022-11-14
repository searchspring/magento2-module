<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\ConfigurableProductsProvider;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigurableProductsProviderTest extends TestCase
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
     * @var ConfigurableProductsProvider
     */
    private $configurableProductsProvider;
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
        $this->configurableProductsProvider = $this->objectManager->get(ConfigurableProductsProvider::class);
        $this->contextManager = $this->objectManager->get(ContextManagerInterface::class);
        $this->assertChildProducts = $this->objectManager->get(AssertChildProducts::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products.php
     *
     * @throws \Exception
     */
    public function testGetData() : void
    {
        $specification = $this->specificationBuilder->build(['includeChildPrices' => true]);
        $products = $this->getProducts->get($specification);
        $data = $this->configurableProductsProvider->getData($products, $specification);
        $config = [
            'products' => [
                'searchspring_configurable_test_configurable' => [
                    'child_count' => 4,
                    'sku_prefix' => 'searchspring_configurable_test_simple_',
                    'name_prefix' => 'SearchSpring Test',
                ],
                'searchspring_configurable_test_configurable_2_attributes' => [
                    'child_count' => 2,
                    'sku_prefix' => 'searchspring_configurable_test_simple_',
                    'name_prefix' => 'SearchSpring Test 2 Attributes'
                ]
            ],
            'required_attributes' => ['child_sku', 'child_sku', 'child_final_price']
        ];

        $this->assertChildProducts->assertChildProducts($data, $config);
        $this->configurableProductsProvider->reset();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture Magento/Catalog/_files/product_boolean_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_decimal_attribute.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products.php
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
        $data = $this->configurableProductsProvider->getData($products, $specification);
        $config = [
            'products' => [
                'searchspring_configurable_test_configurable' => [
                    'child_count' => 4,
                    'sku_prefix' => 'searchspring_configurable_test_simple_',
                    'name_prefix' => 'SearchSpring Test',
                    'value_map' => [
                        'decimal_attribute' => ['10.000000', '20.000000', '30.000000', '40.000000'],
                        'boolean_attribute' => ['Yes', 'Yes', 'Yes', 'Yes']
                    ]
                ],
                'searchspring_configurable_test_configurable_2_attributes' => [
                    'child_count' => 2,
                    'sku_prefix' => 'searchspring_configurable_test_simple_',
                    'name_prefix' => 'SearchSpring Test 2 Attributes',
                    'value_map' => [
                        'decimal_attribute' => ['50.000000', '60.000000'],
                        'boolean_attribute' => ['Yes', 'Yes']
                    ]
                ]
            ],
            'required_attributes' => ['child_sku', 'child_name', 'child_final_price'],
            'additional_attributes' => ['boolean_attribute', 'decimal_attribute']
        ];

        $this->assertChildProducts->assertChildProducts($data, $config);
        $this->configurableProductsProvider->reset();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products.php
     *
     * @throws \Exception
     */
    public function testGetDataWithoutChildPrice() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $products = $this->getProducts->get($specification);
        $data = $this->configurableProductsProvider->getData($products, $specification);
        $config = [
            'products' => [
                'searchspring_configurable_test_configurable' => [
                    'child_count' => 4,
                    'sku_prefix' => 'searchspring_configurable_test_simple_',
                    'name_prefix' => 'SearchSpring Test',
                ],
                'searchspring_configurable_test_configurable_2_attributes' => [
                    'child_count' => 2,
                    'sku_prefix' => 'searchspring_configurable_test_simple_',
                    'name_prefix' => 'SearchSpring Test 2 Attributes'
                ]
            ],
            'required_attributes' => ['child_sku', 'child_name'],
            'restricted_attributes' => ['child_final_price']
        ];

        $this->assertChildProducts->assertChildProducts($data, $config);
        $this->configurableProductsProvider->reset();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products_with_store_value.php
     *
     * @throws \Exception
     */
    public function testGetDataWithMultistoreValues() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $this->contextManager->setContextFromSpecification($specification);
        $products = $this->getProducts->get($specification);
        $data = $this->configurableProductsProvider->getData($products, $specification);
        $config = [
            'products' => [
                'searchspring_configurable_test_configurable' => [
                    'child_count' => 4,
                    'sku_prefix' => 'searchspring_configurable_test_simple_',
                    'name_prefix' => 'Store Default SearchSpring Test',
                ],
                'searchspring_configurable_test_configurable_2_attributes' => [
                    'child_count' => 2,
                    'sku_prefix' => 'searchspring_configurable_test_simple_',
                    'name_prefix' => 'Store Default SearchSpring Test 2 Attributes'
                ]
            ],
            'required_attributes' => ['child_sku', 'child_name'],
        ];

        $this->assertChildProducts->assertChildProducts($data, $config);
        $this->contextManager->resetContext();
        $this->configurableProductsProvider->reset();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products_disabled_simple.php
     *
     * @throws \Exception
     */
    public function testGetDataWithDisabledSimples() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $products = $this->getProducts->get($specification);
        $data = $this->configurableProductsProvider->getData($products, $specification);
        foreach ($data as $product) {
            $this->assertArrayNotHasKey('child_sku', $product);
            $this->assertArrayNotHasKey('child_name', $product);
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products.php
     *
     * @throws \Exception
     */
    public function testReset() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $products = $this->getProducts->get($specification);
        $this->configurableProductsProvider->getData($products, $specification);
        $this->configurableProductsProvider->reset();
        $this->assertTrue(true);
    }
}
