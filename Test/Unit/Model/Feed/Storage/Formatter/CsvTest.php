<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Storage\Formatter;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\FieldsProvider;
use SearchSpring\Feed\Model\Feed\Storage\Formatter\Csv;

class CsvTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var JsonSerializer
     */
    private $jsonMock;

    /**
     * @var FieldsProvider
     */
    private $fieldsProviderMock;

    public function setUp(): void
    {
        $this->jsonMock = $this->createMock(JsonSerializer::class);
        $this->fieldsProviderMock = $this->createMock(FieldsProvider::class);
        $this->csv = new Csv(
            $this->jsonMock,
            $this->fieldsProviderMock
        );
    }

    public function testFormat()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $this->fieldsProviderMock->expects($this->any())
            ->method('getFields')
            ->with($feedSpecificationMock)
            ->willReturn(['field1', 'field2', 'field3']);
        $feedSpecificationMock->expects($this->any())
            ->method('getMultiValuedSeparator')
            ->willReturn(';');

        $this->assertSame(
            [
                [
                    'value1',
                    '',
                    ''
                ],
                [
                    '',
                    'value2',
                    ''
                ],
                [
                    '',
                    '',
                    'value3'
                ],
            ],
            $this->csv->format(
                [
                    ['field1' => 'value1'],
                    ['field2' => 'value2'],
                    ['field3' => 'value3']
                ],
                $feedSpecificationMock
            )
        );
    }
}
