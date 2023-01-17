<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed;

use SearchSpring\Feed\Model\Feed\DataProviderPool;

class DataProviderPoolTest extends \PHPUnit\Framework\TestCase
{
    private $dataProviders = [
        '__all_attributes__' => null,
        '__configurable_child__' => null,
        '__grouped_child__' => null,
        'prices' => null,
        'stock' => null,
        'saleable' => null,
        'url' => null,
        '__rating__' => null,
        '__categories__' => null,
        'options' => null,
        '__media_gallery__' => null,
        '__json_config__' => null,
    ];

    public function setUp(): void
    {
        $this->dataProviderPool = new DataProviderPool($this->dataProviders);
    }

    public function testGet()
    {
        $result = $this->dataProviders;
        unset($result['options']);

        $this->assertSame($result, $this->dataProviderPool->get(['options']));
    }
}
