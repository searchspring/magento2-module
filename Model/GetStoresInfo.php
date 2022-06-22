<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\View\ConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use SearchSpring\Feed\Api\GetStoresInfoInterface;

class GetStoresInfo implements GetStoresInfoInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ConfigInterface
     */
    private $viewConfig;

    /**
     * GetStoresInfo constructor.
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $viewConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ConfigInterface $viewConfig
    ) {
        $this->storeManager = $storeManager;
        $this->viewConfig = $viewConfig;
    }

    /**
     * @return string
     */
    public function getAsHtml(): string
    {
        $result = "<h1>Stores</h1><ul>";
        $stores = $this->storeManager->getStores();
        foreach($stores as $store) {
            $name = $store->getName();
            $code = $store->getCode();
            $result .= "<li>$name - $code</li>";
        }
        $result .= "</ul>";

        $result .= "<h1>Images</h1><ul>";
        $config = $this->viewConfig->getViewConfig()->read();
        foreach($config['media']['Magento_Catalog']['images'] as $id => $image) {
            $result .= "<li>$id<ul>";
            foreach($image as $attr => $val) {
                $result .= "<li>$attr = $val</li>";
            }
            $result .= "</ul></li>";
        }

        $result .= "</ul>";
        return $result;
    }
}
