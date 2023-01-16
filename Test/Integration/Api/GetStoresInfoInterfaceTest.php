<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Api;

use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Api\GetStoresInfoInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GetStoresInfoInterfaceTest extends TestCase
{
    /**
     * @var GetStoresInfoInterface
     */
    private $getStoresInfo;

    protected function setUp(): void
    {
        $this->getStoresInfo = Bootstrap::getObjectManager()->get(GetStoresInfoInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/store.php
     */
    public function testExecute() : void
    {
        $storesInfo = $this->getStoresInfo->getAsHtml();
        $this->assertStoreCodeInResult($storesInfo, 'test', 'Test Store');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/store.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/second_store.php
     */
    public function testExecuteWithMultiStore() : void
    {
        $storesInfo = $this->getStoresInfo->getAsHtml();
        $this->assertStoreCodeInResult($storesInfo, 'test', 'Test Store');
        $this->assertStoreCodeInResult($storesInfo, 'fixture_second_store', 'Fixture Store');
    }

    /**
     * @param string $result
     * @param string $storeCode
     * @param string $name
     */
    private function assertStoreCodeInResult(string $result, string $storeCode, string $name) : void
    {
        $substr = "<li>$name - $storeCode</li>";
        $this->assertEquals(1, substr_count($result, $substr));
    }
}
