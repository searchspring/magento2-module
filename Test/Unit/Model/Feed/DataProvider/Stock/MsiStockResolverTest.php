<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Stock;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\MsiStockResolver;

class MsiStockResolverTest extends \PHPUnit\Framework\TestCase
{
    private $moduleList = [
        'Magento_InventoryReservationsApi',
        'Magento_InventorySalesApi',
        'Magento_InventoryCatalogApi'
    ];

    public function setUp(): void
    {
        $this->moduleManagerMock = $this->createMock(Manager::class);
        $this->msiStockResolver = new MsiStockResolver($this->moduleManagerMock, $this->moduleList);
    }

    public function testResolve()
    {
        $this->moduleManagerMock->expects($this->any())
            ->method('isEnabled')
            ->willReturn(true);

        $this->msiStockResolver->resolve();
    }

    public function testResolveExceptionCase()
    {
        $this->moduleManagerMock->expects($this->any())
            ->method('isEnabled')
            ->willReturn(false);
        $this->expectException(NoSuchEntityException::class);

        $this->msiStockResolver->resolve();
    }
}
