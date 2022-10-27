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
