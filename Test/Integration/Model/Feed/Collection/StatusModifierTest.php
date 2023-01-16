<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Collection;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\Collection\StatusModifier;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StatusModifierTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var StatusModifier
     */
    private $statusModifier;
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
        $this->statusModifier = $this->objectManager->get(StatusModifier::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        $this->productRepository = $this->objectManager->get(ProductRepositoryInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_disabled.php
     */
    public function testModify() : void
    {
        $specification = $this->specificationBuilder->build([]);
        $collection = $this->getCollection();
        $this->statusModifier->modify($collection, $specification);
        // check that disabled product exist and have disabled status
        $disabledProduct = $this->productRepository->get('searchspring_simple_disabled');
        $this->assertEquals(Status::STATUS_DISABLED, (int) $disabledProduct->getStatus());
        $enabledSkus = ['searchspring_simple_1', 'searchspring_simple_2'];
        foreach ($collection as $item) {
            $this->assertTrue(in_array($item->getSku(), $enabledSkus), 'sku: ' . $item->getSku());
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
