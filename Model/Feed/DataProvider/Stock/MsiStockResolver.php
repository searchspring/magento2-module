<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Stock;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager;

class MsiStockResolver implements StockResolverInterface
{
    /**
     * @var Manager
     */
    private $moduleManager;

    private $moduleList = [
        'Magento_InventoryReservationsApi',
        'Magento_InventorySalesApi',
        'Magento_InventoryCatalogApi'
    ];

    /**
     * MsiStockResolver constructor.
     * @param Manager $moduleManager
     * @param array $moduleList
     */
    public function __construct(
        Manager $moduleManager,
        array $moduleList = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleList = array_merge($this->moduleList, $moduleList);
    }

    /**
     * @return StockProviderInterface
     * @throws NoSuchEntityException
     */
    public function resolve(): StockProviderInterface
    {
        if (!$this->isMsiEnabled()) {
            throw new NoSuchEntityException(__('MSI is not installed'));
        }

        return ObjectManager::getInstance()->get('\SearchSpring\Feed\Model\Feed\DataProvider\Stock\MsiStockProvider');
    }

    /**
     * @return bool
     */
    private function isMsiEnabled() : bool
    {
        $moduleExists = true;
        foreach ($this->moduleList as $moduleName) {
            if (!$this->moduleManager->isEnabled($moduleName)) {
                $moduleExists = false;
                break;
            }
        }

        if (!$moduleExists) {
            return false;
        }

        return true;
    }
}
