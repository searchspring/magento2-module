<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Stock;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager;
use SearchSpring\Feed\Api\LoggerInterface;

class MsiStockResolver implements StockResolverInterface
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var MsiStockProvider
     */
    private $msiStockProvider;
    /**
     * @var LegacyStockProvider
     */
    private $legacyStockProvider;

    private $moduleList = [
        'Magento_InventoryReservationsApi',
        'Magento_InventorySalesApi',
        'Magento_InventoryCatalogApi'
    ];
    /**
     * @var bool|null
     */
    private $isMsiEnabledCache = null;

    /**
     * MsiStockResolver constructor.
     * @param Manager $moduleManager
     * @param MsiStockProvider $msiStockProvider
     * @param LegacyStockProvider $legacyStockProvider
     * @param array $moduleList
     * @param LoggerInterface $logger
     */
    public function __construct(
        Manager $moduleManager,
        MsiStockProvider $msiStockProvider,
        LegacyStockProvider $legacyStockProvider,
        LoggerInterface $logger,
        array $moduleList = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->msiStockProvider = $msiStockProvider;
        $this->legacyStockProvider = $legacyStockProvider;
        $this->logger = $logger;
        $this->moduleList = array_merge($this->moduleList, $moduleList);
    }

    /**
     * @param bool $isMsiEnabled
     * @return StockProviderInterface
     * @throws NoSuchEntityException
     */
    public function resolve(bool $isMsiEnabled): StockProviderInterface
    {
        $isInventoryModulesEnabled = $this->isInventoryModulesEnabled();
        if ($isInventoryModulesEnabled) {
            $this->logger->info(
                'MSI Check',
                [
                    'method' => __METHOD__,
                    'isMsiEnabledViaPayload' => $isMsiEnabled,
                    'isInventoryModulesEnabled' => $isInventoryModulesEnabled,
                    'message' => 'MSI is enabled via payload and MSI modules are enabled. Using MsiStockProvider for stock resolution.'
                ]
            );
            return $this->msiStockProvider;
        }
        $this->logger->info(
            'MSI Check',
            [
                'method' => __METHOD__,
                'isMsiEnabledViaPayload' => $isMsiEnabled,
                'isInventoryModulesEnabled' => $isInventoryModulesEnabled,
                'message' => 'MSI is disabled via payload or MSI modules are not installed. Using LegacyStockProvider for stock resolution.'
            ]
        );
        return $this->legacyStockProvider;
    }

    /**
     * @return bool
     */
    private function isInventoryModulesEnabled(): bool
    {
        if ($this->isMsiEnabledCache !== null) {
            return $this->isMsiEnabledCache;
        }

        foreach ($this->moduleList as $moduleName) {
            if (!$this->moduleManager->isEnabled($moduleName)) {
                $this->isMsiEnabledCache = false;
                return false;
            }
        }

        $this->isMsiEnabledCache = true;
        return true;
    }
}
