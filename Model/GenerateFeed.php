<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\App\DeploymentConfig;
use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\GenerateFeedInterface;
use SearchSpring\Feed\Model\Feed\CollectionProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProviderPool;
use SearchSpring\Feed\Model\Feed\FormatterPool;

class GenerateFeed implements GenerateFeedInterface
{
    const DEFAULT_PAGE_SIZE = 10000;
    /**
     * @var CollectionProviderInterface
     */
    private $collectionProvider;
    /**
     * @var DataProviderPool
     */
    private $dataProviderPool;
    /**
     * @var FormatterPool
     */
    private $formatterPool;
    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * GenerateFeed constructor.
     * @param CollectionProviderInterface $collectionProvider
     * @param DataProviderPool $dataProviderPool
     * @param FormatterPool $formatterPool
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        CollectionProviderInterface $collectionProvider,
        DataProviderPool $dataProviderPool,
        FormatterPool $formatterPool,
        DeploymentConfig $deploymentConfig
    ) {
        $this->collectionProvider = $collectionProvider;
        $this->dataProviderPool = $dataProviderPool;
        $this->formatterPool = $formatterPool;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return FeedInterface
     */
    public function execute(FeedSpecificationInterface $feedSpecification): FeedInterface
    {
        $collection = $this->collectionProvider->getCollection($feedSpecification);
    }
}
