<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Api;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\ExecuteTaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExecuteTaskInterfaceTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var ExecuteTaskInterface
     */
    private $executeTask;
    /**
     * @var TaskRepositoryInterface
     */
    private $taskRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->executeTask = $this->objectManager->get(ExecuteTaskInterface::class);
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
     */
    public function testExecute() : void
    {
        $task = $this->createPendingTask();
        $this->executeTask->execute($task);
        $this->assertEquals(MetadataInterface::TASK_STATUS_SUCCESS, $task->getStatus());
        $this->taskRepository->delete($task);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configure_invalid_generate_feed_mock.php
     *
     * @return void
     * @throws LocalizedException
     */
    public function testExecuteInvalidTask() : void
    {
        $task = $this->createPendingTask();
        $this->executeTask->execute($task);
        $this->assertEquals(MetadataInterface::TASK_STATUS_ERROR, $task->getStatus());
        $this->assertNotNull($task->getError());
        $error = $task->getError();
        $this->assertNotNull($error->getCode());
        $this->assertNotNull($error->getMessage());
        $this->taskRepository->delete($task);
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
