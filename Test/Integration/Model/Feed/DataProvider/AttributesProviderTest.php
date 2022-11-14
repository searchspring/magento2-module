<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\DataProvider\AttributesProvider;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AttributesProviderTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var AttributesProvider
     */
    private $attributesProvider;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;
    /**
     * @var GetProducts
     */
    private $getProducts;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->attributesProvider = $this->objectManager->get(AttributesProvider::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        $this->getProducts = $this->objectManager->get(GetProducts::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture Magento/Catalog/_files/product_boolean_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_decimal_attribute.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     */
    public function testGetData() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $products = $this->getProducts->get($specification);
        $data = $this->attributesProvider->getData($products, $specification);
        $testAttributes = ['boolean_attribute', 'decimal_attribute'];
        foreach ($data as $item) {
            $keys = array_keys($item);
            foreach ($testAttributes as $attribute) {
                $this->assertTrue(in_array($attribute, $keys));
                $this->assertNotNull($item[$attribute] ?? null);
            }
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_boolean_attribute.php
     * @magentoDataFixture Magento/Catalog/_files/product_decimal_attribute.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     */
    public function testReset() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $products = $this->getProducts->get($specification);
        $this->attributesProvider->getData($products, $specification);
        $this->attributesProvider->reset();
        $this->assertTrue(true);
    }
}
