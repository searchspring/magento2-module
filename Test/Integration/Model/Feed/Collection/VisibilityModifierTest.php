<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\Collection\VisibilityModifier;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class VisibilityModifierTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var VisibilityModifier
     */
    private $visibilityModifier;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->visibilityModifier = $this->objectManager->get(VisibilityModifier::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_not_visible.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_visibility_catalog.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_visibility_search.php
     */
    public function testModify() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $collection = $this->getCollection();
        $this->visibilityModifier->modify($collection, $specification);
        $skus = [];
        foreach ($collection as $item) {
            $skus[] = $item->getSku();
        }

        $this->assertTrue(!in_array('searchspring_simple_not_visible', $skus));
        $this->assertTrue(in_array('searchspring_simple_visibility_catalog', $skus));
        $this->assertTrue(in_array('searchspring_simple_visibility_search', $skus));
        $this->assertTrue(in_array('searchspring_simple_1', $skus));
        $this->assertTrue(in_array('searchspring_simple_2', $skus));
    }

    /**
     * @return Collection
     */
    private function getCollection() : Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
