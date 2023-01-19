<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed;

use SearchSpring\Feed\Api\AppConfigInterface;
use SearchSpring\Feed\Model\Feed\CollectionConfig;

class CollectionConfigTest extends \PHPUnit\Framework\TestCase
{
    private $appConfigMock;

    private $collectionConfig;

    public function setUp(): void
    {
        $this->appConfigMock = $this->createMock(AppConfigInterface::class);
        $this->collectionConfig = new CollectionConfig($this->appConfigMock);
    }

    public function testGetPageSize()
    {
        $pageSize = 1500;
        $this->appConfigMock->expects($this->once())
            ->method('getValue')
            ->with(CollectionConfig::PAGE_SIZE_CONFIG_PATH)
            ->willReturn($pageSize);

        $this->assertSame($pageSize, $this->collectionConfig->getPageSize());
    }
}
