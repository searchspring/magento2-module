<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Api;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\ExecutePendingTasksInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExecutePendingTasksInterfaceTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var ExecutePendingTasksInterface
     */
    private $executePendingTasks;
    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->executePendingTasks = $this->objectManager->get(ExecutePendingTasksInterface::class);
        $this->taskRepository = $this->objectManager->get(TaskRepositoryInterface::class);
        $this->searchCriteriaBuilder = $this->objectManager->get(SearchCriteriaBuilder::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configure_generate_feed_mock.php
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function testExecute() : void
    {
        $task = $this->createPendingTask();
        $result = $this->executePendingTasks->execute();
        $this->assertCount(1, $result);
        $task = $this->taskRepository->get($task->getEntityId());
        $this->assertEquals(MetadataInterface::TASK_STATUS_SUCCESS, $task->getStatus());
    }
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configure_generate_feed_mock.php
     *
     * @return void
     * @throws LocalizedException
     */
    public function testExecuteWithNoTaskInDb() : void
    {
        $result = $this->executePendingTasks->execute();
        $this->assertEmpty($result);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $tasks = $this->taskRepository->getList($searchCriteria)->getItems();
        $this->assertEmpty($tasks);
    }
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configure_generate_feed_mock.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/processing_task.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/success_task.php
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function testExecuteWithNoPendingTaskInDb() : void
    {
        $result = $this->executePendingTasks->execute();
        $this->assertEmpty($result);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $tasks = $this->taskRepository->getList($searchCriteria)->getItems();
        $this->assertNotEmpty($tasks);
    }

    /**
     * @return TaskInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function createPendingTask() : TaskInterface
    {
        /** @var TaskInterface $task */
        $task = $this->objectManager->create(TaskInterface::class);
        $task->setPayload($this->getPayload())
            ->setType(MetadataInterface::FEED_GENERATION_TASK_CODE)
            ->setStatus(MetadataInterface::TASK_STATUS_PENDING);

        return $this->taskRepository->save($task);
    }

    /**
     * @return array
     */
    private function getPayload() : array
    {
        return [
            'preSignedUrl' => 'https://testurl.com'
        ];
    }
}
