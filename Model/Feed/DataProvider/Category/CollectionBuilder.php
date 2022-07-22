<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Category;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class CollectionBuilder
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * CollectionBuilder constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param array $categoryIds
     * @param FeedSpecificationInterface $feedSpecification
     * @return Collection
     * @throws LocalizedException
     */
    public function buildCollection(
        array $categoryIds,
        FeedSpecificationInterface $feedSpecification
    ) : Collection {
        $collection = $this->collectionFactory->create();
        $collection->setStore($feedSpecification->getStoreCode());
        $selectAttributes = [
            CategoryInterface::KEY_NAME,
            CategoryInterface::KEY_IS_ACTIVE,
            CategoryInterface::KEY_PATH
        ];
        if ($feedSpecification->getIncludeMenuCategories()) {
            $selectAttributes[] = CategoryInterface::KEY_INCLUDE_IN_MENU;
        }

        if ($feedSpecification->getIncludeUrlHierarchy()) {
            $collection->addUrlRewriteToResult();
        }

        $collection->addAttributeToSelect($selectAttributes);
        $collection->addAttributeToFilter(CategoryInterface::KEY_IS_ACTIVE, 1)
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds]);

        return $collection;
    }
}
