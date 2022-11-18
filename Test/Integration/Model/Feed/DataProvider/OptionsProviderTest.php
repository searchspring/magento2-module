<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\OptionsProvider;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OptionsProviderTest extends TestCase
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
     * @var OptionsProvider
     */
    private $optionsProvider;
    /**
     * @var ContextManagerInterface
     */
    private $contextManager;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        $this->getProducts = $this->objectManager->get(GetProducts::class);
        $this->optionsProvider = $this->objectManager->get(OptionsProvider::class);
        $this->contextManager = $this->objectManager->get(ContextManagerInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products_with_options.php
     *
     * @throws \Exception
     */
    public function testGetData() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $products = $this->getProducts->get($specification);
        $data = $this->optionsProvider->getData($products, $specification);
        $map = [
            'searchspring_simple_1' => [
                'option_drop_down_option_1' => [
                    'drop_down option 1 1', 'drop_down option 2 1'
                ]
            ],
            'searchspring_simple_2' => [
                'option_drop_down_option_2' => [
                    'drop_down option 1 2', 'drop_down option 2 2'
                ]
            ]
        ];

        $restrictedOptions = [
            'searchspring_simple_1' => [
                'test_option_code_1', 'area_option', 'multiple_option', 'date_option'
            ],
        ];

        $this->assertOptions($data, $map, $restrictedOptions);
        $this->optionsProvider->reset();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products_with_options_multistore.php
     *
     * @throws \Exception
     */
    public function testGetDataMultistore() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $this->contextManager->setContextFromSpecification($specification);
        $products = $this->getProducts->get($specification);
        $data = $this->optionsProvider->getData($products, $specification);
        $map = [
            'searchspring_simple_1' => [
                'option_store_default_drop_down_option_1' => [
                    'Store Default drop_down option 1 1', 'Store Default drop_down option 2 1'
                ]
            ],
            'searchspring_simple_2' => [
                'option_store_default_drop_down_option_2' => [
                    'Store Default drop_down option 1 2', 'Store Default drop_down option 2 2'
                ]
            ]
        ];

        $restrictedOptions = [
            'searchspring_simple_1' => [
                'test_option_code_1', 'area_option', 'multiple_option', 'date_option'
            ],
        ];

        $this->assertOptions($data, $map, $restrictedOptions);
        $this->optionsProvider->reset();
        $this->contextManager->resetContext();
    }

    /**
     * @param array $products
     * @param array $map
     * @param array $restrictedOptions
     */
    private function assertOptions(array $products, array $map, array $restrictedOptions = []) : void
    {
        foreach ($products as $product) {
            /** @var Product $productModel */
            $productModel = $product['product_model'] ?? null;
            if (!$productModel) {
                continue;
            }

            $sku = $productModel->getSku();
            $optionsMap = $map[$sku];
            foreach ($optionsMap as $optionKey => $optionValues) {
                $this->assertArrayHasKey($optionKey, $product);
                $this->assertEmpty(array_diff($product[$optionKey], $optionValues));
                $this->assertEmpty(array_diff($optionValues, $product[$optionKey]));
            }

            $restrictedOptionsBySku = $restrictedOptions[$sku] ?? [];
            foreach ($restrictedOptionsBySku as $restrictedOption) {
                $this->assertArrayNotHasKey($restrictedOption, $product);
            }
        }
    }
}
