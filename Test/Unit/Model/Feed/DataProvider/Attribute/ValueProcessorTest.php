<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Attribute;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Model\Feed\DataProvider\Attribute\ValueProcessor;

class ValueProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->valueProcessor = new ValueProcessor();
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetValue()
    {
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $attributeMock->expects($this->once())
            ->method('usesSource')
            ->willReturn(false);
        $attributeMock->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn('test');

        $this->assertSame(
            'test',
            $this->valueProcessor->getValue($attributeMock, 'test')
        );
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetValueException()
    {
        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $attributeMock->expects($this->once())
            ->method('usesSource')
            ->willReturn(false);
        $attributeMock->expects($this->at(0))
            ->method('getAttributeCode')
            ->willReturn($attributeMock);
        $attributeMock->expects($this->at(1))
            ->method('getAttributeCode')
            ->willReturn('test');
        $attributeMock->expects($this->at(2))
            ->method('getAttributeCode')
            ->willReturn('test');

        $this->expectException(\Exception::class);

        $this->valueProcessor->getValue($attributeMock, $attributeMock);
    }
}
