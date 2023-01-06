<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Collection;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\Collection\TierPriceProcessor;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TierPriceProcessorTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var TierPriceProcessor
     */
    private $tierPriceProcessor;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->tierPriceProcessor = $this->objectManager->get(TierPriceProcessor::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products_tierprice.php
     */
    public function testProcess() : void
    {
        $specification = $this->specificationBuilder->build(['includeTierPricing' => true]);
        $collection = $this->getCollection();
        $this->tierPriceProcessor->processAfterLoad($collection, $specification);
        $this->assertTrue($collection->getFlag('tier_price_added'));
        foreach ($collection as $item) {
            /** @var $item Product */
            if ($item->getSku() === 'searchspring_simple_1') {
                $this->assertNotEmpty($item->getTierPrices());
                $this->assertNotEmpty($item->getData('tier_price'));
            } else {
                $this->assertEmpty($item->getTierPrices());
                $this->assertEmpty($item->getData('tier_price'));
            }
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products_tierprice.php
     */
    public function testProcessWithAddTierPriceDataIsFalse() : void
    {
        $specification = $this->specificationBuilder->build(['includeTierPricing' => false]);
        $collection = $this->getCollection();
        $this->tierPriceProcessor->processAfterLoad($collection, $specification);
        $this->assertNull($collection->getFlag('tier_price_added'));
        foreach ($collection as $item) {
            /** @var $item Product */
            $this->assertNull($item->getData('tier_price'));
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
