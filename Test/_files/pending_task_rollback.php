<?php

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();
/** @var TaskRepositoryInterface $taskRepository */
$taskRepository = $objectManager->get(TaskRepositoryInterface::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $objectManager->get(SearchCriteriaBuilder::class);
$searchCriteria = $searchCriteriaBuilder->addFilter(TaskInterface::ENTITY_ID,1)
    ->create();
foreach ($taskRepository->getList($searchCriteria)->getItems() as $task) {
    $taskRepository->delete($task);
}
