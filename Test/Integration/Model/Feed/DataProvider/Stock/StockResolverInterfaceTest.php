<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider\Stock;

use Magento\Framework\Module\Manager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\LegacyStockProvider;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\MsiStockProvider;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\StockResolverInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockResolverInterfaceTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var Manager
     */
    private $moduleManager;
    /**
     * @var StockResolverInterface
     */
    private $resolver;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->moduleManager = $this->objectManager->get(Manager::class);
        $this->resolver = $this->objectManager->get(StockResolverInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     *
     * @throws \Exception
     */
    public function testResolveMsi() : void
    {
        if (!$this->isMsiEnabled()) {
            $this->markTestSkipped('MSI is disabled');
        }

        $this->assertInstanceOf(MsiStockProvider::class, $this->resolver->resolve());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     *
     * @throws \Exception
     */
    public function testResolveLegacy() : void
    {
        if ($this->isMsiEnabled()) {
            $this->markTestSkipped('MSI is enabled');
        }

        $this->assertInstanceOf(LegacyStockProvider::class, $this->resolver->resolve());
    }

    /**
     * @return bool
     */
    private function isMsiEnabled() : bool
    {
        return $this->moduleManager->isEnabled('Magento_Inventory');
    }
}
