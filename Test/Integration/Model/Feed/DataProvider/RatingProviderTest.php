<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RatingProviderTest extends TestCase
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

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        $this->getProducts = $this->objectManager->get(GetProducts::class);
        parent::setUp();
    }

    public function testGetData() : void
    {

    }

    public function testReset() : void
    {

    }
}
