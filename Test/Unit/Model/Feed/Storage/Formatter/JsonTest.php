<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Storage\Formatter;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Storage\Formatter\Json;

class JsonTest extends \PHPUnit\Framework\TestCase
{
    private $json;

    public function setUp(): void
    {
        $this->json = new Json();
    }

    public function testFormat()
    {
        $data = [
            'test' => 'test',
            'test1' => ['test']
        ];
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $this->assertSame($data, $this->json->format($data, $feedSpecificationMock));
    }
}
