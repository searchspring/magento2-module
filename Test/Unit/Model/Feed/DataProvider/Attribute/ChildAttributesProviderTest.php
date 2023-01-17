<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Attribute;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ChildAttributesProvider;
use SearchSpring\Feed\Model\Feed\Specification\Feed;

class ChildAttributesProviderTest extends \PHPUnit\Framework\TestCase
{
    private $eavConfigMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->eavConfigMock = $this->createMock(Config::class);
        $this->childAttributesProvider = new ChildAttributesProvider($this->eavConfigMock);
    }

    /**
     * @return void
     */
    public function testGetAttributes()
    {
        $abstractAttributeMock = $this->getMockBuilder(AbstractAttribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $feedSpecificationMock = $this->getMockBuilder(Feed::class)
            ->disableOriginalConstructor()
            ->getMock();
        $feedSpecificationMock->expects($this->once())
            ->method('getChildFields')
            ->willReturn(['test']);
        $this->eavConfigMock->expects($this->any())
            ->method('getAttribute')
            ->withAnyParameters()
            ->willReturn($abstractAttributeMock);
        $abstractAttributeMock->expects($this->any())
            ->method('getAttributeId')
            ->willReturn(0);

        $this->assertSame(
            [$abstractAttributeMock],
            $this->childAttributesProvider->getAttributes($feedSpecificationMock)
        );
    }
}
