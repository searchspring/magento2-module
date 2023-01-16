<?php

declare(strict_types=1);

use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\CatalogRule\Model\Indexer\IndexBuilder;
use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\CatalogRule\Model\Rule;
use Magento\TestFramework\Helper\Bootstrap;

require __DIR__ . '/simple_products_rollback.php';

$objectManager = Bootstrap::getObjectManager();
/** @var CatalogRuleRepositoryInterface $ruleRepository */
$ruleRepository = $objectManager->create(CatalogRuleRepositoryInterface::class);
/** @var IndexBuilder $indexBuilder */
$indexBuilder = $objectManager->get(IndexBuilder::class);
/** @var CollectionFactory $ruleCollectionFactory */
$ruleCollectionFactory = $objectManager->get(CollectionFactory::class);
$ruleCollection = $ruleCollectionFactory->create()
    ->addFieldToFilter('name', ['like' => 'Searchspring Test Simple CG%']);
/** @var Rule $rule */
$rule = $ruleCollection->getFirstItem();
foreach ($ruleCollection as $rule) {
    $ruleRepository->delete($rule);
}
$indexBuilder->reindexFull();
