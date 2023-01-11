<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use SearchSpring\Feed\Api\AppConfigInterface;

class CollectionConfig implements CollectionConfigInterface
{
    const DEFAULT_PAGE_SIZE = 2000;
    const PAGE_SIZE_CONFIG_PATH = 'product_page_size';
    /**
     * @var AppConfigInterface
     */
    private $appConfig;

    /**
     * CollectionConfig constructor.
     * @param AppConfigInterface $appConfig
     */
    public function __construct(
        AppConfigInterface $appConfig
    ) {
        $this->appConfig = $appConfig;
    }

    /**
     * @return int
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function getPageSize(): int
    {
        $pageSize = $this->appConfig->getValue(self::PAGE_SIZE_CONFIG_PATH);
        return $pageSize ? (int) $pageSize : self::DEFAULT_PAGE_SIZE;
    }
}
