<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Storage\File;

use Magento\Framework\Stdlib\DateTime\DateTime;
use SearchSpring\Feed\Model\Feed\Storage\File\NameGenerator;

class NameGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private $dateTimeMock;

    private $nameGenerator;

    public function setUp(): void
    {
        $this->dateTimeMock = $this->createMock(DateTime::class);
        $this->nameGenerator = new NameGenerator($this->dateTimeMock);
    }

    public function testGenerate()
    {
        $testDateTime = '2000-02-20 00:00:00';
        $testOption = 'test';
        $this->dateTimeMock->expects($this->once())
            ->method('gmtDate')
            ->willReturn($testDateTime);
        $this->assertSame(
            'searchspring_' . $testOption . '_2000_02_20_00_00_00',
            $this->nameGenerator->generate([$testOption])
        );
    }
}
