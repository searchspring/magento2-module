<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;

class CollectionConfig implements CollectionConfigInterface
{
    const DEFAULT_PAGE_SIZE = 2000;
    const PAGE_SIZE_CONFIG_PATH = 'searchspring/feed/page_size';
    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * CollectionConfig constructor.
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        DeploymentConfig $deploymentConfig
    ) {
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * @return int
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function getPageSize(): int
    {
        $pageSize = $this->deploymentConfig->get(self::PAGE_SIZE_CONFIG_PATH);
        return $pageSize ? (int) $pageSize : self::DEFAULT_PAGE_SIZE;
    }
}
