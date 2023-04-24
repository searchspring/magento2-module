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

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Storage\File;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Math\Random;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\FieldsProvider;
use SearchSpring\Feed\Model\Feed\Storage\File\Csv;

class CsvTest extends \PHPUnit\Framework\TestCase
{
    private $filesystemMock;

    private $randomMock;

    private $fieldsProviderMock;

    private $csv;

    public function setUp(): void
    {
        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->randomMock = $this->createMock(Random::class);
        $this->fieldsProviderMock = $this->createMock(FieldsProvider::class);
        $this->csv = new Csv(
            $this->filesystemMock,
            $this->randomMock,
            $this->fieldsProviderMock
        );
    }

    public function testInitialize()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $writeFileMock = $this->createMock(Filesystem\File\WriteInterface::class);
        $writeDirectoryMock = $this->createMock(Filesystem\Directory\WriteInterface::class);
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($writeDirectoryMock);
        $writeDirectoryMock->expects($this->once())
            ->method('isExist')
            ->with('searchspring/test.csv')
            ->willReturn(false);
        $writeDirectoryMock->expects($this->once())
            ->method('openFile')
            ->with('searchspring/test.csv')
            ->willReturn($writeFileMock);
        $writeDirectoryMock->expects($this->once())
            ->method('isFile')
            ->willReturn(true);

        $this->csv->initialize('test', $feedSpecificationMock);
    }

    public function testAppendData()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $writeFileMock = $this->createMock(Filesystem\File\WriteInterface::class);
        $writeDirectoryMock = $this->createMock(Filesystem\Directory\WriteInterface::class);
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::VAR_DIR)
            ->willReturn($writeDirectoryMock);
        $writeDirectoryMock->expects($this->once())
            ->method('isExist')
            ->with('searchspring/test.csv')
            ->willReturn(false);
        $writeDirectoryMock->expects($this->once())
            ->method('openFile')
            ->with('searchspring/test.csv')
            ->willReturn($writeFileMock);
        $writeDirectoryMock->expects($this->exactly(2))
            ->method('isFile')
            ->willReturn(true);
        $writeFileMock->expects($this->at(0))
            ->method('writeCsv')
            ->with([], ',', '"');
        $writeFileMock->expects($this->at(1))
            ->method('writeCsv')
            ->with(['data'], ',', '"');
        $this->csv->initialize('test', $feedSpecificationMock);
        $this->csv->appendData([['data']]);
    }

    public function testAppendDataExceptionCase()
    {
        $this->expectException(\Exception::class);
        $this->csv->appendData([]);
    }
}
