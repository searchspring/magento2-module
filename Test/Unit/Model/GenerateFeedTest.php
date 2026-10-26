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

namespace SearchSpring\Feed\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\AppConfigInterface;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Model\Feed\Collection\ProcessCollectionInterface;
use SearchSpring\Feed\Model\Feed\Collection\ProcessorPool;
use SearchSpring\Feed\Model\Feed\CollectionConfigInterface;
use SearchSpring\Feed\Model\Feed\CollectionProviderInterface;
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProviderPool;
use SearchSpring\Feed\Model\Feed\Specification\Feed;
use SearchSpring\Feed\Model\Feed\StorageInterface;
use SearchSpring\Feed\Model\Feed\SystemFieldsList;
use SearchSpring\Feed\Model\GenerateFeed;
use SearchSpring\Feed\Model\Metric\CollectorInterface;

/**
 * Call order is verified with callbacks instead of $this->at() / withConsecutive(),
 * both removed in PHPUnit 10, so the test runs on PHPUnit 9 and 10.
 */
class GenerateFeedTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CollectionProviderInterface|MockObject
     */
    private $collectionProviderMock;

    /**
     * @var DataProviderPool|MockObject
     */
    private $dataProviderPoolMock;

    /**
     * @var CollectionConfigInterface|MockObject
     */
    private $collectionConfigMock;

    /**
     * @var StorageInterface|MockObject
     */
    private $storageMock;

    /**
     * @var SystemFieldsList|MockObject
     */
    private $systemFieldsListMock;

    /**
     * @var ContextManagerInterface|MockObject
     */
    private $contextManagerMock;

    /**
     * @var ProcessorPool|MockObject
     */
    private $afterLoadProcessorPoolMock;

    /**
     * @var AppConfigInterface|MockObject
     */
    private $appConfigMock;

    /**
     * @var CollectorInterface|MockObject
     */
    private $metricCollectorMock;

    /**
     * @var TaskRepositoryInterface|MockObject
     */
    private $taskRepositoryMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var GenerateFeed
     */
    private $generateFeed;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->collectionProviderMock = $this->createMock(CollectionProviderInterface::class);
        $this->dataProviderPoolMock = $this->createMock(DataProviderPool::class);
        $this->collectionConfigMock = $this->createMock(CollectionConfigInterface::class);
        $this->storageMock = $this->createMock(StorageInterface::class);
        $this->systemFieldsListMock = $this->createMock(SystemFieldsList::class);
        $this->contextManagerMock = $this->createMock(ContextManagerInterface::class);
        $this->afterLoadProcessorPoolMock = $this->createMock(ProcessorPool::class);
        $this->metricCollectorMock = $this->createMock(CollectorInterface::class);
        $this->appConfigMock = $this->createMock(AppConfigInterface::class);
        $this->taskRepositoryMock = $this->createMock(TaskRepositoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->generateFeed = new GenerateFeed(
            $this->collectionProviderMock,
            $this->dataProviderPoolMock,
            $this->collectionConfigMock,
            $this->storageMock,
            $this->systemFieldsListMock,
            $this->contextManagerMock,
            $this->afterLoadProcessorPoolMock,
            $this->metricCollectorMock,
            $this->appConfigMock,
            $this->taskRepositoryMock,
            $this->loggerMock
        );
    }

    public function testExecute()
    {
        $pageSize = 10;
        $format = 'format';

        $dataProviderMock = $this->createMock(DataProviderInterface::class);
        $dataProviderMockSecond = $this->createMock(DataProviderInterface::class);
        $processCollectionInterfaceMock = $this->createMock(ProcessCollectionInterface::class);
        $processCollectionInterfaceMockSecond = $this->createMock(ProcessCollectionInterface::class);
        $collectionMock = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $feedSpecificationMock = $this->getMockBuilder(Feed::class)->disableOriginalConstructor()->getMock();
        $productMock = $this->createMock(Product::class);
        $productMockSecond = $this->createMock(Product::class);
        $taskMock = $this->createMock(TaskInterface::class);
        $dataProviders = [
            'data_provider_1' => $dataProviderMock,
            'data_provider_2' => $dataProviderMockSecond,
        ];

        $feedSpecificationMock->expects($this->once())
            ->method('getPreSignedUrl')
            ->willReturn('https://example.com/path/to/file.json.gz');
        $feedSpecificationMock->expects($this->once())
            ->method('getFormat')
            ->willReturn($format);
        $feedSpecificationMock->expects($this->any())
            ->method('getIgnoreFields')
            ->willReturn(['test']);
        $this->storageMock->expects($this->once())
            ->method('isSupportedFormat')
            ->with($format)
            ->willReturn(true);
        $this->storageMock->expects($this->any())
            ->method('getAdditionalData')
            ->willReturn(
                [
                    'name' => 'test',
                    'size' => 333
                ]
            );
        $this->contextManagerMock->expects($this->once())
            ->method('setContextFromSpecification')
            ->with($feedSpecificationMock);
        $this->contextManagerMock->expects($this->once())
            ->method('resetContext');
        $this->storageMock->expects($this->once())
            ->method('initiate')
            ->with($feedSpecificationMock);
        $this->collectionProviderMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);
        $this->collectionConfigMock->expects($this->once())
            ->method('getPageSize')
            ->willReturn($pageSize);
        $collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with($pageSize);
        $collectionMock->expects($this->once())
            ->method('getLastPageNumber')
            ->willReturn(2);
        $this->appConfigMock->expects($this->once())
            ->method('getValue')
            ->with('product_metric_max_page')
            ->willReturn(10);
        $this->dataProviderPoolMock->expects($this->any())
            ->method('get')
            ->with(['test'])
            ->willReturn($dataProviders);
        $this->afterLoadProcessorPoolMock->expects($this->any())
            ->method('getAll')
            ->willReturn([$processCollectionInterfaceMock, $processCollectionInterfaceMockSecond]);
        $this->systemFieldsListMock->expects($this->any())
            ->method('add')
            ->with('product_model');
        $productMock->expects($this->any())
            ->method('getEntityId')
            ->willReturn(1);
        $productMockSecond->expects($this->any())
            ->method('getEntityId')
            ->willReturn(2);

        $calls = [];
        $collectionMock->expects($this->exactly(2))
            ->method('setCurPage')
            ->willReturnCallback(function (int $page) use (&$calls, $collectionMock) {
                $calls[] = 'setCurPage:' . $page;
                return $collectionMock;
            });
        $collectionMock->expects($this->exactly(2))
            ->method('getItems')
            ->willReturnOnConsecutiveCalls([$productMock], [$productMockSecond]);
        $collectionMock->expects($this->exactly(2))
            ->method('load')
            ->willReturnSelf();
        $collectionMock->expects($this->exactly(2))
            ->method('clear');

        foreach ([$processCollectionInterfaceMock, $processCollectionInterfaceMockSecond] as $processor) {
            $processor->expects($this->exactly(2))
                ->method('processAfterLoad')
                ->with($collectionMock, $feedSpecificationMock);
            $processor->expects($this->exactly(2))
                ->method('processAfterFetchItems')
                ->with($collectionMock, $feedSpecificationMock);
        }

        // each data provider adds its own field to the rows returned by the previous one
        $values = [
            'data_provider_1' => [1 => 'value_1', 2 => 'value_2'],
            'data_provider_2' => [1 => 'value_3', 2 => 'value_4'],
        ];
        $receivedRows = [];
        foreach ($dataProviders as $key => $dataProvider) {
            $dataProvider->expects($this->exactly(2))
                ->method('reset');
            $dataProvider->expects($this->exactly(2))
                ->method('resetAfterFetchItems');
            $dataProvider->expects($this->exactly(2))
                ->method('getData')
                ->with($this->isType('array'), $feedSpecificationMock)
                ->willReturnCallback(function (array $rows) use ($key, $values, &$receivedRows) {
                    $receivedRows[$key][] = $rows;
                    foreach ($rows as &$row) {
                        $row[$key] = $values[$key][$row['entity_id']];
                    }
                    return $rows;
                });
        }

        $storedRows = [];
        $this->storageMock->expects($this->exactly(2))
            ->method('addData')
            ->willReturnCallback(function (array $rows, $id) use (&$storedRows, &$calls) {
                $this->assertSame(1, $id);
                $calls[] = 'addData';
                $storedRows[] = $rows;
            });
        $this->storageMock->expects($this->once())
            ->method('commit')
            ->with(1);
        $this->storageMock->expects($this->never())
            ->method('rollback');
        $this->metricCollectorMock->expects($this->once())
            ->method('reset')
            ->with(CollectorInterface::CODE_PRODUCT_FEED);

        $this->taskRepositoryMock->expects($this->once())
            ->method('get')
            ->with(1)
            ->willReturn($taskMock);
        $taskMock->expects($this->once())
            ->method('setProductCount')
            ->with(2);
        $this->taskRepositoryMock->expects($this->once())
            ->method('save')
            ->with($taskMock);

        $infoLogs = [];
        $this->loggerMock->expects($this->any())
            ->method('info')
            ->willReturnCallback(function (string $message, array $context = []) use (&$infoLogs) {
                $infoLogs[$message] = $context;
            });

        $this->generateFeed->execute($feedSpecificationMock, 1);

        $this->assertSame(['setCurPage:1', 'addData', 'setCurPage:2', 'addData'], $calls);
        $this->assertSame(
            [
                [['entity_id' => 1, 'product_model' => $productMock]],
                [['entity_id' => 2, 'product_model' => $productMockSecond]],
            ],
            $receivedRows['data_provider_1']
        );
        $this->assertSame(
            [
                [['entity_id' => 1, 'product_model' => $productMock, 'data_provider_1' => 'value_1']],
                [['entity_id' => 2, 'product_model' => $productMockSecond, 'data_provider_1' => 'value_2']],
            ],
            $receivedRows['data_provider_2']
        );
        $this->assertSame(
            [
                [[
                    'entity_id' => 1,
                    'product_model' => $productMock,
                    'data_provider_1' => 'value_1',
                    'data_provider_2' => 'value_3',
                ]],
                [[
                    'entity_id' => 2,
                    'product_model' => $productMockSecond,
                    'data_provider_1' => 'value_2',
                    'data_provider_2' => 'value_4',
                ]],
            ],
            $storedRows
        );

        $this->assertSame(
            ['data_provider_1', 'data_provider_2'],
            array_keys($infoLogs['Feed data providers']['dataProviders'])
        );
        $timingLog = $infoLogs['Feed data providers execution time in seconds, slowest first'];
        $this->assertEqualsCanonicalizing(['data_provider_1', 'data_provider_2'], array_keys($timingLog['timings']));
        $this->assertSame(2, $timingLog['pageCount']);
        $this->assertSame(2, $timingLog['productCount']);
    }

    public function testExecuteLogsDataProviderPerPageOnlyInDebugMode()
    {
        $dataProviderMock = $this->createMock(DataProviderInterface::class);
        $dataProviderMock->method('getData')->willReturnArgument(0);
        $collectionMock = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $collectionMock->method('getLastPageNumber')->willReturn(2);
        $collectionMock->method('getItems')->willReturn([$this->createMock(Product::class)]);
        $feedSpecificationMock = $this->getMockBuilder(Feed::class)->disableOriginalConstructor()->getMock();
        $feedSpecificationMock->method('getPreSignedUrl')->willReturn('https://example.com/path/to/file.json');
        $feedSpecificationMock->method('getFormat')->willReturn('json');
        $feedSpecificationMock->method('getIgnoreFields')->willReturn([]);
        $this->storageMock->method('isSupportedFormat')->willReturn(true);
        $this->storageMock->method('getAdditionalData')->willReturn([]);
        $this->collectionProviderMock->method('getCollection')->willReturn($collectionMock);
        $this->dataProviderPoolMock->method('get')->willReturn(['prices' => $dataProviderMock]);
        $this->afterLoadProcessorPoolMock->method('getAll')->willReturn([]);
        $this->taskRepositoryMock->method('get')->willReturn($this->createMock(TaskInterface::class));
        $this->appConfigMock->method('isDebug')->willReturn(true);

        $debugLogs = [];
        $this->loggerMock->expects($this->exactly(2))
            ->method('debug')
            ->willReturnCallback(function (string $message, array $context = []) use (&$debugLogs) {
                $debugLogs[] = $context;
            });

        $this->generateFeed->execute($feedSpecificationMock, 1);

        $this->assertSame([1, 2], array_column($debugLogs, 'page'));
        $this->assertSame(['prices', 'prices'], array_column($debugLogs, 'dataProvider'));
        $this->assertSame([1, 1], array_column($debugLogs, 'rows'));
    }

    public function testExecuteExceptionCase()
    {
        $pageSize = 10;
        $format = 'format';

        $collectionMock = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $feedSpecificationMock = $this->getMockBuilder(Feed::class)->disableOriginalConstructor()->getMock();
        $feedSpecificationMock->expects($this->once())
            ->method('getFormat')
            ->willReturn($format);
        $feedSpecificationMock->expects($this->once())
            ->method('getPreSignedUrl')
            ->willReturn('https://example.com/path/to/file.json.gz');
        $this->storageMock->expects($this->once())
            ->method('isSupportedFormat')
            ->with($format)
            ->willReturn(true);
        $this->storageMock->expects($this->any())
            ->method('getAdditionalData')
            ->willReturn(
                [
                    'name' => 'test',
                    'size' => 333
                ]
            );
        $this->contextManagerMock->expects($this->once())
            ->method('setContextFromSpecification')
            ->with($feedSpecificationMock);
        $this->storageMock->expects($this->once())
            ->method('initiate')
            ->with($feedSpecificationMock);
        $this->collectionProviderMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($collectionMock);
        $this->collectionConfigMock->expects($this->once())
            ->method('getPageSize')
            ->willReturn($pageSize);
        $collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with($pageSize);
        $collectionMock->expects($this->once())
            ->method('getLastPageNumber')
            ->willReturn(2);
        $this->appConfigMock->expects($this->once())
            ->method('getValue')
            ->with('product_metric_max_page')
            ->willReturn(10);
        $collectionMock->expects($this->once())
            ->method('setCurPage')
            ->with(1)
            ->willThrowException(new \Exception());
        $this->storageMock->expects($this->once())
            ->method('rollback');
        $this->storageMock->expects($this->never())
            ->method('commit');
        $this->taskRepositoryMock->expects($this->never())
            ->method('save');
        $this->contextManagerMock->expects($this->once())
            ->method('resetContext');
        $this->expectException(\Exception::class);
        $this->generateFeed->execute($feedSpecificationMock, 1);
    }

    public function testExecuteResetsPartialContextWhenContextSetupFails()
    {
        $feedSpecificationMock = $this->getMockBuilder(Feed::class)->disableOriginalConstructor()->getMock();
        $feedSpecificationMock->method('getPreSignedUrl')->willReturn('https://example.com/path/to/file.json');
        $feedSpecificationMock->method('getFormat')->willReturn('json');
        $feedSpecificationMock->method('getIgnoreFields')->willReturn([]);
        $this->storageMock->method('isSupportedFormat')->willReturn(true);
        $this->storageMock->method('getAdditionalData')->willReturn([]);
        $this->dataProviderPoolMock->method('get')->willReturn([]);
        // e.g. store emulation started, then the customer of the task does not exist anymore
        $this->contextManagerMock->expects($this->once())
            ->method('setContextFromSpecification')
            ->willThrowException(new \Magento\Framework\Exception\NoSuchEntityException(__('No such customer')));
        $this->contextManagerMock->expects($this->once())
            ->method('resetContext');
        $this->storageMock->expects($this->never())
            ->method('initiate');
        $this->collectionProviderMock->expects($this->never())
            ->method('getCollection');

        $this->expectException(\Magento\Framework\Exception\NoSuchEntityException::class);
        $this->generateFeed->execute($feedSpecificationMock, 1);
    }

    public function testExecuteResetsContextWhenCommitFails()
    {
        $collectionMock = $this->getMockBuilder(Collection::class)->disableOriginalConstructor()->getMock();
        $collectionMock->method('getLastPageNumber')->willReturn(1);
        $collectionMock->method('getItems')->willReturn([]);
        $feedSpecificationMock = $this->getMockBuilder(Feed::class)->disableOriginalConstructor()->getMock();
        $feedSpecificationMock->method('getPreSignedUrl')->willReturn('https://example.com/path/to/file.json');
        $feedSpecificationMock->method('getFormat')->willReturn('json');
        $feedSpecificationMock->method('getIgnoreFields')->willReturn([]);
        $this->storageMock->method('isSupportedFormat')->willReturn(true);
        $this->storageMock->method('getAdditionalData')->willReturn([]);
        $this->collectionProviderMock->method('getCollection')->willReturn($collectionMock);
        $this->dataProviderPoolMock->method('get')->willReturn([]);
        $this->afterLoadProcessorPoolMock->method('getAll')->willReturn([]);
        $this->taskRepositoryMock->method('get')->willReturn($this->createMock(TaskInterface::class));
        $this->storageMock->expects($this->once())
            ->method('commit')
            ->willThrowException(new \RuntimeException('upload failed'));
        $this->contextManagerMock->expects($this->once())
            ->method('resetContext');

        $this->expectExceptionMessage('upload failed');
        $this->generateFeed->execute($feedSpecificationMock, 1);
    }

    public function testExecuteExceptionCaseOnUnsupportedFormat()
    {
        $format = 'format';
        $feedSpecificationMock = $this->getMockBuilder(Feed::class)->disableOriginalConstructor()->getMock();
        $feedSpecificationMock->expects($this->once())
            ->method('getFormat')
            ->willReturn($format);
        $feedSpecificationMock->expects($this->once())
            ->method('getPreSignedUrl')
            ->willReturn('https://example.com/path/to/file.json.gz');
        $this->storageMock->expects($this->once())
            ->method('isSupportedFormat')
            ->with($format)
            ->willReturn(false);
        $this->storageMock->expects($this->never())
            ->method('initiate');
        $this->contextManagerMock->expects($this->never())
            ->method('resetContext');
        $this->expectExceptionMessage('format is not supported format');
        $this->expectException(\Exception::class);
        $this->generateFeed->execute($feedSpecificationMock, 1);
    }
}
