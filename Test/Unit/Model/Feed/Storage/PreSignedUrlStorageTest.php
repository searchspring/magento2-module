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

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Storage;

use SearchSpring\Feed\Api\AppConfigInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Aws\PreSignedUrl;
use SearchSpring\Feed\Model\Feed\Storage\File\FileFactory;
use SearchSpring\Feed\Model\Feed\Storage\File\NameGenerator;
use SearchSpring\Feed\Model\Feed\Storage\FileInterface;
use SearchSpring\Feed\Model\Feed\Storage\FormatterInterface;
use SearchSpring\Feed\Model\Feed\Storage\FormatterPool;
use SearchSpring\Feed\Model\Feed\Storage\PreSignedUrlStorage;

class PreSignedUrlStorageTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormatterPool
     */
    private $formatterPoolMock;

    /**
     * @var PreSignedUrl
     */
    private $preSignedUrlMock;

    /**
     * @var NameGenerator
     */
    private $nameGeneratorMock;

    /**
     * @var FileFactory
     */
    private $fileFactoryMock;

    /**
     * @var AppConfigInterface
     */
    private $appConfigMock;

    public function setUp(): void
    {
        $this->formatterPoolMock = $this->createMock(FormatterPool::class);
        $this->preSignedUrlMock = $this->createMock(PreSignedUrl::class);
        $this->nameGeneratorMock = $this->createMock(NameGenerator::class);
        $this->fileFactoryMock = $this->createMock(FileFactory::class);
        $this->appConfigMock = $this->createMock(AppConfigInterface::class);
        $this->preSignedUrlStorage = new PreSignedUrlStorage(
            $this->formatterPoolMock,
            $this->preSignedUrlMock,
            $this->nameGeneratorMock,
            $this->fileFactoryMock,
            $this->appConfigMock,
        );
    }

    public function testIsSupportedFormat()
    {
        $testFormat = 'format';
        $formatterInterfaceMock = $this->createMock(FormatterInterface::class);
        $this->formatterPoolMock->expects($this->once())
            ->method('get')
            ->with($testFormat)
            ->willReturn($formatterInterfaceMock);
        $this->fileFactoryMock->expects($this->once())
            ->method('isSupportedFormat')
            ->with($testFormat)
            ->willReturn(true);

        $this->assertSame(true, $this->preSignedUrlStorage->isSupportedFormat($testFormat));
    }

    public function testIsSupportedFormatFalseCase()
    {
        $testFormat = 'format_false';
        $formatterInterfaceMock = $this->createMock(FormatterInterface::class);
        $this->formatterPoolMock->expects($this->once())
            ->method('get')
            ->with($testFormat)
            ->willReturn($formatterInterfaceMock);
        $this->fileFactoryMock->expects($this->once())
            ->method('isSupportedFormat')
            ->with($testFormat)
            ->willReturn(false);

        $this->assertSame(false, $this->preSignedUrlStorage->isSupportedFormat($testFormat));
    }

    public function testInitiate()
    {
        $testFormat = 'spformat';
        $fileMock = $this->createMock(FileInterface::class);
        $feedSpecificationMock = $this->createMock(FeedSpecificationInterface::class);
        $formatterInterfaceMock = $this->createMock(FormatterInterface::class);
        $feedSpecificationMock->expects($this->any())
            ->method('getFormat')
            ->willReturn($testFormat);
        $this->formatterPoolMock->expects($this->once())
            ->method('get')
            ->with($testFormat)
            ->willReturn($formatterInterfaceMock);
        $this->fileFactoryMock->expects($this->once())
            ->method('isSupportedFormat')
            ->with($testFormat)
            ->willReturn(true);
        $this->fileFactoryMock->expects($this->once())
            ->method('create')
            ->with($testFormat)
            ->willReturn($fileMock);
        $this->nameGeneratorMock->expects($this->once())
            ->method('generate')
            ->with(['product', 'aws_presigned'])
            ->willReturn('generated_name');
        $fileMock->expects($this->once())
            ->method('initialize')
            ->with('generated_name', $feedSpecificationMock);

        $this->preSignedUrlStorage->initiate($feedSpecificationMock);
    }

    public function testInitiateExceptionCase()
    {
        $testFormat = null;
        $feedSpecificationMock = $this->createMock(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->any())
            ->method('getFormat')
            ->willReturn($testFormat);
        $this->expectExceptionMessage('format cannot be empty');
        $this->expectException(\Exception::class);
        $this->preSignedUrlStorage->initiate($feedSpecificationMock);
    }

    public function testInitiateExceptionSecondCase()
    {
        $testFormat = 'spformat';
        $feedSpecificationMock = $this->createMock(FeedSpecificationInterface::class);
        $formatterInterfaceMock = $this->createMock(FormatterInterface::class);
        $feedSpecificationMock->expects($this->any())
            ->method('getFormat')
            ->willReturn($testFormat);
        $this->formatterPoolMock->expects($this->once())
            ->method('get')
            ->with($testFormat)
            ->willReturn($formatterInterfaceMock);
        $this->fileFactoryMock->expects($this->once())
            ->method('isSupportedFormat')
            ->with($testFormat)
            ->willReturn(false);
        $this->expectExceptionMessage($testFormat . ' is not supported format');
        $this->expectException(\Exception::class);
        $this->preSignedUrlStorage->initiate($feedSpecificationMock);
    }

    public function testAddDataExceptionFileCase()
    {
        $this->expectExceptionMessage('file is not initialized yet');
        $this->expectException(\Exception::class);
        $this->preSignedUrlStorage->addData([]);
    }

    public function testAddDataExceptionSpecificationCase()
    {
        $fileMock = $this->createMock(FileInterface::class);
        $preSignedUrlStorage = new \ReflectionClass(PreSignedUrlStorage::class);
        $file = $preSignedUrlStorage->getProperty('file');
        $file->setAccessible(true);
        $file->setValue($this->preSignedUrlStorage, $fileMock);
        $this->expectExceptionMessage('specification is not initialized yet');
        $this->expectException(\Exception::class);
        $this->preSignedUrlStorage->addData([]);
    }

    public function testAddDataExceptionEmptyFormatCase()
    {
        $feedSpecificationMock = $this->createMock(FeedSpecificationInterface::class);
        $fileMock = $this->createMock(FileInterface::class);
        $preSignedUrlStorage = new \ReflectionClass(PreSignedUrlStorage::class);
        $file = $preSignedUrlStorage->getProperty('file');
        $file->setAccessible(true);
        $file->setValue($this->preSignedUrlStorage, $fileMock);
        $specification = $preSignedUrlStorage->getProperty('specification');
        $specification->setAccessible(true);
        $specification->setValue($this->preSignedUrlStorage, $feedSpecificationMock);
        $feedSpecificationMock->expects($this->once())
            ->method('getFormat')
            ->willReturn('');
        $this->expectExceptionMessage('format cannot be empty');
        $this->expectException(\Exception::class);
        $this->preSignedUrlStorage->addData([]);
    }

    public function testAddData()
    {
        $testFormat = 'apformat';
        $testData = ['test' => 'data'];
        $feedSpecificationMock = $this->createMock(FeedSpecificationInterface::class);
        $fileMock = $this->createMock(FileInterface::class);
        $formatterInterfaceMock = $this->createMock(FormatterInterface::class);

        $preSignedUrlStorage = new \ReflectionClass(PreSignedUrlStorage::class);
        $file = $preSignedUrlStorage->getProperty('file');
        $file->setAccessible(true);
        $file->setValue($this->preSignedUrlStorage, $fileMock);
        $specification = $preSignedUrlStorage->getProperty('specification');
        $specification->setAccessible(true);
        $specification->setValue($this->preSignedUrlStorage, $feedSpecificationMock);
        $feedSpecificationMock->expects($this->once())
            ->method('getFormat')
            ->willReturn($testFormat);
        $this->fileFactoryMock->expects($this->once())
            ->method('isSupportedFormat')
            ->with($testFormat)
            ->willReturn(true);
        $this->formatterPoolMock->expects($this->any())
            ->method('get')
            ->with($testFormat)
            ->willReturn($formatterInterfaceMock);
        $formatterInterfaceMock->expects($this->once())
            ->method('format')
            ->with($testData, $feedSpecificationMock)
            ->willReturn(array_merge($testData, ['formatted' => true]));
        $fileMock->expects($this->once())
            ->method('appendData')
            ->with([
                'test' => 'data',
                'formatted' => true
            ]);

        $this->preSignedUrlStorage->addData($testData);
    }

    public function testCommitExceptionCase()
    {
        $this->expectExceptionMessage('file is not initialized yet');
        $this->expectException(\Exception::class);
        $this->preSignedUrlStorage->commit();
    }

    public function testCommit()
    {
        $fileMock = $this->createMock(FileInterface::class);
        $feedSpecificationMock = $this->createMock(FeedSpecificationInterface::class);
        $absolutePath = 'abs/olute/path/to/file.exe';
        $preSignedUrlStorage = new \ReflectionClass(PreSignedUrlStorage::class);
        $file = $preSignedUrlStorage->getProperty('file');
        $file->setAccessible(true);
        $file->setValue($this->preSignedUrlStorage, $fileMock);
        $fileMock->expects($this->once())
            ->method('commit');
        $fileMock->expects($this->once())
            ->method('getAbsolutePath')
            ->willReturn($absolutePath);
        $specification = $preSignedUrlStorage->getProperty('specification');
        $specification->setAccessible(true);
        $specification->setValue($this->preSignedUrlStorage, $feedSpecificationMock);
        $this->preSignedUrlMock->expects($this->once())
            ->method('save')
            ->with($feedSpecificationMock, ['type' => 'stream', 'file' => $absolutePath]);
        $this->appConfigMock->expects($this->once())
            ->method('isDebug')
            ->willReturn(false);
        $fileMock->expects($this->once())
            ->method('delete');

        $this->preSignedUrlStorage->commit();
    }

    public function testRollback()
    {
        $fileMock = $this->createMock(FileInterface::class);
        $preSignedUrlStorage = new \ReflectionClass(PreSignedUrlStorage::class);
        $file = $preSignedUrlStorage->getProperty('file');
        $file->setAccessible(true);
        $file->setValue($this->preSignedUrlStorage, $fileMock);
        $fileMock->expects($this->once())
            ->method('rollback');

        $this->preSignedUrlStorage->rollback();
    }

    public function testGetAdditionalData()
    {
        $fileMock = $this->createMock(FileInterface::class);
        $preSignedUrlStorage = new \ReflectionClass(PreSignedUrlStorage::class);
        $file = $preSignedUrlStorage->getProperty('file');
        $file->setAccessible(true);
        $file->setValue($this->preSignedUrlStorage, $fileMock);
        $fileMock->expects($this->once())
            ->method('getFileInfo')
            ->willReturn([
                'size' => 333,
                'blocks' => 3333,
            ]);
        $fileMock->expects($this->once())
            ->method('getName')
            ->willReturn('test_name');

        $this->assertSame(
            [
                'size' => 333,
                'blocks' => 3333,
                'name' => 'test_name'
            ],
            $this->preSignedUrlStorage->getAdditionalData()
        );
    }
}
