<?php

namespace SearchSpring\Feed\Test\Unit\Model\Task\GenerateFeed;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Api\Data\TaskSearchResultsInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Model\Task;
use SearchSpring\Feed\Model\Task\GenerateFeed\UniqueChecker;

class UniqueCheckerTest extends TestCase
{
    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepositoryMock;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilderMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->taskRepositoryMock = $this->createMock(TaskRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->uniqueChecker = new UniqueChecker(
            $this->taskRepositoryMock,
            $this->searchCriteriaBuilderMock
        );
    }

    /**
     * @return void
     * @dataProvider checkDataProvider
     * @throws LocalizedException
     */
    public function testCheck(array $payload, bool $result)
    {
        $data = [
            'stores' => 'default',
            'customerId' => 1,
            'childFields' => [
                'manufacturer',
                'country_of_manufacturer'
            ],
            'includeOutOfStock' => 1
        ];

        $searchResultsMock = $this->getMockBuilder(TaskSearchResultsInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $taskMock = $this->getMockBuilder(Task::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);
        $this->searchCriteriaBuilderMock->expects($this->any())
            ->method('addFilter')
            ->withAnyParameters()
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->any())
            ->method('create')
            ->willReturn($searchCriteriaMock);
        $this->taskRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$taskMock]);

        $taskMock->expects($this->once())
            ->method('getPayload')
            ->willReturn($data);

        $this->assertSame($result, $this->uniqueChecker->check($payload));
    }

    /**
     * @return array[]
     */
    public function checkDataProvider(): array
    {
        return [
            [
                [
                    'stores' => 'default',
                    'customerId' => 1,
                    'childFields' => [
                        'manufacturer',
                        'country_of_manufacturer'
                    ],
                    'includeOutOfStock' => 1
                ],
                false
            ],
            [
                [
                    'stores' => 'default',
                    'customerId' => 2,
                    'childFields' => [
                        'manufacturer',
                        'country_of_manufacturer'
                    ],
                    'includeOutOfStock' => 1
                ],
                true
            ],
            [
                [
                    'stores' => 'web',
                    'customerId' => 2,
                    'childFields' => [
                        'country_of_manufacturer',
                        'manufacturer'
                    ],
                    'includeOutOfStock' => 1
                ],
                true
            ],
            [
                [
                    'stores' => 'default',
                    'customerId' => 1,
                    'childFields' => [
                        'country_of_manufacturer',
                        'manufacturer'
                    ],
                    'includeOutOfStock' => 1
                ],
                false
            ]
        ];
    }
}
