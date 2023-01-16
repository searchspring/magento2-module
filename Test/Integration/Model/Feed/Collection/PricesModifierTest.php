<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\Collection\PricesModifier;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PricesModifierTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var PricesModifier
     */
    private $pricesModifier;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->pricesModifier = $this->objectManager->get(PricesModifier::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     */
    public function testModify() : void
    {
        $this->assertWithoutPriceRestriction([]);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     */
    public function testModifyWithIgnoreAllPricesFields() : void
    {
        $payload = ['ignoreFields' => ['final_price', 'regular_price', 'max_price']];
        $specification = $this->specificationBuilder->build($payload);
        $collection = $this->getCollection();
        $this->pricesModifier->modify($collection, $specification);
        foreach ($collection as $item) {
            $this->assertNull($item->getData('final_price'));
            $this->assertNull($item->getData('minimal_price'));
            $this->assertNull($item->getData('min_price'));
            $this->assertNull($item->getData('max_price'));
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     */
    public function testModifyWithIgnoreOneOrTwoPricesField() : void
    {
        $this->assertWithoutPriceRestriction(['ignoreFields' => ['final_price']]);
        $this->assertWithoutPriceRestriction(['ignoreFields' => ['regular_price']]);
        $this->assertWithoutPriceRestriction(['ignoreFields' => ['max_price']]);
        $this->assertWithoutPriceRestriction(['ignoreFields' => ['max_price', 'regular_price']]);
    }

    /**
     * @param array $payload
     */
    private function assertWithoutPriceRestriction(array $payload) : void
    {
        $specification = $this->specificationBuilder->build($payload);
        $collection = $this->getCollection();
        $this->pricesModifier->modify($collection, $specification);
        foreach ($collection as $item) {
            $this->assertNotNull($item->getData('final_price'));
            $this->assertNotNull($item->getData('minimal_price'));
            $this->assertNotNull($item->getData('min_price'));
            $this->assertNotNull($item->getData('max_price'));
        }
    }

    /**
     * @return Collection
     */
    private function getCollection() : Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
