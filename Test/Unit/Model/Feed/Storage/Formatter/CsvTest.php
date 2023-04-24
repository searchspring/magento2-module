<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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

    private $csv;

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
        $this->jsonMock->expects($this->once())
            ->method('serialize')
            ->with([['value2'], 'value3'])
            ->willReturn(json_encode([['value2'], 'value3']));

        $this->assertSame(
            [
                [
                    'value1',
                    '[["value2"],"value3"]',
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
                    ['field1' => 'value1', 'field2' => [['value2'], 'value3']],
                    ['field2' => 'value2'],
                    ['field3' => 'value3']
                ],
                $feedSpecificationMock
            )
        );
    }
}
