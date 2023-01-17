<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed;

use SearchSpring\Feed\Model\Feed\SystemFieldsList;

class SystemFieldsListTest extends \PHPUnit\Framework\TestCase
{
    private $testData = ['test'];

    public function setUp(): void
    {
        $this->systemFieldsList = new SystemFieldsList($this->testData);
    }

    public function testGet()
    {
        $this->assertSame($this->testData, $this->systemFieldsList->get());
    }

    public function testAdd()
    {
        $testField = 'test1';
        $this->systemFieldsList->add($testField);
        $this->assertSame(array_merge($this->testData, [$testField]), $this->systemFieldsList->get());
    }

    public function testIsSystem()
    {
        $this->assertSame(true, $this->systemFieldsList->isSystem('test'));
    }
}
