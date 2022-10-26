<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Task\GenerateFeed;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Exception\GenericException;
use SearchSpring\Feed\Model\Task\GenerateFeed\Executor;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ExecutorTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var Executor
     */
    private $executor;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->executor = $this->objectManager->get(Executor::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configure_aws_client_mock.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     *
     * @return void
     * @throws CouldNotSaveException
     * @throws GenericException
     */
    public function testExecute() : void
    {
        $task = $this->createPendingTask();
        $this->executor->execute($task);
        // check that we achieve this place and dont have any exceptions
        $this->assertEquals(1,1);
    }

    /**
     * @return TaskInterface
     * @throws CouldNotSaveException
     */
    private function createPendingTask() : TaskInterface
    {
        /** @var TaskInterface $task */
        $task = $this->objectManager->create(TaskInterface::class);
        $task->setPayload($this->getPayload())
            ->setType(MetadataInterface::FEED_GENERATION_TASK_CODE)
            ->setStatus(MetadataInterface::TASK_STATUS_PENDING);

        return $task;
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
