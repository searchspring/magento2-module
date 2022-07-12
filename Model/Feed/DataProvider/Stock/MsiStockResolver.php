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
        'Magento_InventorySalesApi'
    ];
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * MsiStockResolver constructor.
     * @param Manager $moduleManager
     * @param ObjectManager $objectManager
     * @param array $moduleList
     */
    public function __construct(
        Manager $moduleManager,
        ObjectManager $objectManager,
        array $moduleList = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
        $this->moduleList = array_merge($this->moduleList, $moduleList);
    }

    /**
     * @return StockProviderInterface
     * @throws NoSuchEntityException
     */
    public function resolve(): StockProviderInterface
    {
        $moduleExists = true;
        foreach ($this->moduleList as $moduleName) {
            if (!$this->moduleManager->isEnabled($moduleName)) {
                $moduleExists = false;
                break;
            }
        }

        if (!$moduleExists) {
            throw new NoSuchEntityException(__('MSI is not installed'));
        }

        return $this->objectManager->get('\SearchSpring\Feed\Model\Feed\DataProvider\Stock\MsiStockProvider');
    }
}
