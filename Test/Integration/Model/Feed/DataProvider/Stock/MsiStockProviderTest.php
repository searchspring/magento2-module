<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider\Stock;

use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\MsiStockProvider;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;
use SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider\GetProducts;

class MsiStockProviderTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var GetProducts
     */
    private $getProducts;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var MsiStockProvider
     */
    private $stockProvider;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->getProducts = $this->objectManager->get(GetProducts::class);
        $this->storeManager = $this->objectManager->get(StoreManagerInterface::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        $this->stockProvider = $this->objectManager->get(MsiStockProvider::class);
        parent::setUp();
    }

    public function testGetStock() : void
    {
    }
}
