<?php

use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();
/** @var TaskRepositoryInterface $taskRepository */
$taskRepository = $objectManager->get(TaskRepositoryInterface::class);
$payload = [
    'preSignedUrl' => 'https://testurl.com'
];
/** @var TaskInterface $task */
$task = $objectManager->create(TaskInterface::class);
$task->setPayload($payload)
    ->setType(MetadataInterface::FEED_GENERATION_TASK_CODE)
    ->setStatus(MetadataInterface::TASK_STATUS_PROCESSING);

$taskRepository->save($task);
