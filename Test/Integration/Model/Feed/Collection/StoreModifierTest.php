<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\Collection\StoreModifier;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreModifierTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var mixed
     */
    private $storeModifier;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->storeModifier = $this->objectManager->get(StoreModifier::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        parent::setUp();
    }

    public function testModify() : void
    {

    }

    /**
     * @return Collection
     */
    private function getCollection() : Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
