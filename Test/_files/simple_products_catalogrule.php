<?php

use Magento\CatalogRule\Api\CatalogRuleRepositoryInterface;
use Magento\CatalogRule\Model\Indexer\IndexBuilder;
use Magento\CatalogRule\Model\Rule;
use Magento\CatalogRule\Model\Rule\Condition\Combine;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Customer\Model\Group;
use Magento\Framework\App\Area;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('SearchSpring_Feed::Test/_files/simple_products.php');
Bootstrap::getInstance()->loadArea(Area::AREA_ADMINHTML);
/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
/** @var WebsiteRepositoryInterface $websiteRepository */
$websiteRepository = $objectManager->get(WebsiteRepositoryInterface::class);
/** @var CatalogRuleRepositoryInterface $ruleRepository */
$ruleRepository = $objectManager->get(CatalogRuleRepositoryInterface::class);
/** @var Rule $rule */
$rule = $objectManager->get(RuleFactory::class)->create();
$rule->loadPost(
    [
        'name' => 'Searchspring Test Simple Percent rule for grouped product',
        'is_active' => '1',
        'stop_rules_processing' => 0,
        'website_ids' => [$websiteRepository->get('base')->getId()],
        'customer_group_ids' => Group::NOT_LOGGED_IN_ID,
        'discount_amount' => 50,
        'simple_action' => 'by_percent',
        'from_date' => '',
        'to_date' => '',
        'sort_order' => 0,
        'sub_is_enable' => 0,
        'sub_discount_amount' => 0,
        'conditions' => [
            '1' => ['type' => Combine::class, 'aggregator' => 'all', 'value' => '1', 'new_child' => ''],
            '1--1' => ['type' => Rule\Condition\Product::class, 'attribute' => 'sku', 'operator' => '==', 'value' => 'searchspring_simple_1'],
        ],
    ]
);
$ruleRepository->save($rule);

$rule = $objectManager->get(RuleFactory::class)->create();
$rule->loadPost(
    [
        'name' => 'Searchspring Test Simple Rule For Product 1',
        'is_active' => '1',
        'stop_rules_processing' => 0,
        'website_ids' => [$websiteRepository->get('base')->getId()],
        'customer_group_ids' => Group::NOT_LOGGED_IN_ID,
        'discount_amount' => 2,
        'simple_action' => 'by_fixed',
        'from_date' => '',
        'to_date' => '',
        'sort_order' => 100,
        'sub_is_enable' => 0,
        'sub_discount_amount' => 0,
        'conditions' => [
            '1' => ['type' => Combine::class, 'aggregator' => 'all', 'value' => '1', 'new_child' => ''],
            '1--1' => ['type' => Rule\Condition\Product::class, 'attribute' => 'sku', 'operator' => '==', 'value' => 'searchspring_simple_1'],
        ],
    ]
);
$ruleRepository->save($rule);

$rule = $objectManager->get(RuleFactory::class)->create();
$rule->loadPost(
    [
        'name' => 'Searchspring Test Simple Rule For Product 2',
        'is_active' => '1',
        'stop_rules_processing' => 0,
        'website_ids' => [$websiteRepository->get('base')->getId()],
        'customer_group_ids' => Group::NOT_LOGGED_IN_ID,
        'discount_amount' => 4,
        'simple_action' => 'by_fixed',
        'from_date' => '',
        'to_date' => '',
        'sort_order' => 100,
        'sub_is_enable' => 0,
        'sub_discount_amount' => 0,
        'conditions' => [
            '1' => ['type' => Combine::class, 'aggregator' => 'all', 'value' => '1', 'new_child' => ''],
            '1--1' => ['type' => Rule\Condition\Product::class, 'attribute' => 'sku', 'operator' => '==', 'value' => 'searchspring_simple_2'],
        ],
    ]
);
$ruleRepository->save($rule);

/** @var IndexBuilder $indexBuilder */
$indexBuilder = $objectManager->get(IndexBuilder::class);
$indexBuilder->reindexFull();

$objectManager->get(IndexerRegistry::class)
    ->get('catalog_product_price')
    ->reindexAll();
