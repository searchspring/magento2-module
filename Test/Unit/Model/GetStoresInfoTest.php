<?php

namespace SearchSpring\Feed\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\ConfigInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use SearchSpring\Feed\Model\GetStoresInfo;

class GetStoresInfoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var StoreManagerInterface&MockObject
     */
    private $storeManagerMock;

    /**
     * @var ConfigInterface&MockObject
     */
    private $viewConfigMock;

    /**
     * @var Emulation&MockObject
     */
    private $emulationMock;

    /**
     * @var ScopeConfigInterface&MockObject
     */
    private $scopeConfigMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->viewConfigMock = $this->createMock(ConfigInterface::class);
        $this->emulationMock = $this->createMock(Emulation::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->getStoresInfoModel = new GetStoresInfo(
            $this->storeManagerMock,
            $this->viewConfigMock,
            $this->emulationMock,
            $this->scopeConfigMock
        );
    }

    public function testGetAsHtml()
    {
        $result = '<h1>Stores</h1><ul></ul>';
        $this->storeManagerMock->expects($this->once())
            ->method('getStores')
            ->willReturn([]);

        $this->assertSame($result, $this->getStoresInfoModel->getAsHtml());
    }

    public function testGetAsJson()
    {
        $result = [];
        $this->storeManagerMock->expects($this->once())
            ->method('getStores')
            ->willReturn([]);

        $this->assertSame($result, $this->getStoresInfoModel->getAsJson());
    }
}
