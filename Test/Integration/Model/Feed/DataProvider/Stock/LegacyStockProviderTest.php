<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\DataProvider;

use SearchSpring\Feed\Model\Feed\DataProvider\Stock\LegacyStockProvider;

class LegacyStockProviderTest extends StockProviderTest
{
    /**
     * @var LegacyStockProvider
     */
    private $stockProvider;

    /**
     *
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->stockProvider = $this->objectManager->get(LegacyStockProvider::class);
    }

    /**
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoConfigFixture current_store cataloginventory/options/show_out_of_stock 1
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_oos.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configurable_products_oos_simples.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/grouped_products.php
     *
     * @throws \Exception
     */
    public function testGetStock() : void
    {
        $config = [
            'searchspring_simple_1' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_simple_2' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_simple_oos' => ['in_stock' => 0, 'qty' => 100],
            'searchspring_configurable_test_configurable' => ['in_stock' => 1, 'qty' => 0],
            'searchspring_configurable_test_configurable_2_attributes' => ['in_stock' => 1, 'qty' => 0],
            'searchspring_configurable_test_oos_simple_configurable' => ['in_stock' => 0, 'qty' => 0],
            'searchspring_grouped_test_simple_1000' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_grouped_test_simple_1001' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_grouped_test_simple_1010' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_grouped_test_simple_1011' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_grouped_test_simple_1012' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_grouped_test_simple_1013' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_grouped_test_grouped_1' => ['in_stock' => 1, 'qty' => 0],
            'searchspring_grouped_test_grouped_2' => ['in_stock' => 1, 'qty' => 0],
        ];

        $specificationConfig = ['includeOutOfStock' => true];
        $this->executeTest($this->stockProvider, $config, $specificationConfig);
    }

    /**
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoConfigFixture current_store cataloginventory/options/show_out_of_stock 1
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_not_manage_stock.php
     * @throws \Exception
     */
    public function testGetStockNoManageStock() : void
    {
        $config = [
            'searchspring_simple_not_manage_stock' => ['in_stock' => 1, 'qty' => 0],
        ];
        $specificationConfig = ['includeOutOfStock' => true];
        $this->executeTest($this->stockProvider, $config, $specificationConfig);
    }

    /**
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoConfigFixture current_store cataloginventory/options/show_out_of_stock 1
     * @magentoConfigFixture current_store cataloginventory/item_options/manage_stock 0
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_oos.php
     * @throws \Exception
     */
    public function testGetStockNoManageStockFromConfig() : void
    {
        $config = [
            'searchspring_simple_1' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_simple_2' => ['in_stock' => 1, 'qty' => 100],
            'searchspring_simple_oos' => ['in_stock' => 1, 'qty' => 100],
        ];
        $specificationConfig = ['includeOutOfStock' => true];
        $this->executeTest($this->stockProvider, $config, $specificationConfig);
    }

    /**
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoConfigFixture current_store cataloginventory/options/show_out_of_stock 1
     * @magentoConfigFixture current_store cataloginventory/item_options/manage_stock 1
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_product_oos.php
     * @magentoConfigFixture fixturestore_store cataloginventory/options/show_out_of_stock 1
     * @magentoConfigFixture fixturestore_store cataloginventory/item_options/manage_stock 0
     * @throws \Exception
     */
    public function testGetStockNoManageStockFromConfigMultistore() : void
    {
        $config = [
            'searchspring_simple_oos' => ['in_stock' => 0, 'qty' => 100],
        ];
        $specificationConfig = ['includeOutOfStock' => true];
        $this->executeTest($this->stockProvider, $config, $specificationConfig);
        $storeId = (int) $this->storeManager->getStore('fixturestore')->getId();
        $config = [
            'searchspring_simple_oos' => ['in_stock' => 1, 'qty' => 100],
        ];
        $this->executeTest($this->stockProvider, $config, $specificationConfig, $storeId);
    }
}
