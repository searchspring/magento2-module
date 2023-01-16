<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Context;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\Context\StoreContextManager;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreContextManagerTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var StoreContextManager
     */
    private $storeContextManager;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->storeContextManager = $this->objectManager->get(StoreContextManager::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        $this->storeManager = $this->objectManager->get(StoreManagerInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetContextFromSpecification() : void
    {
        $storeCode = 'default';
        $this->assertStore($storeCode);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetContextFromSpecificationWithEmptyStore() : void
    {
        $defaultCode = 'default';
        $specification = $this->specificationBuilder->build(['store' => null]);
        $this->storeContextManager->setContextFromSpecification($specification);
        $this->assertEquals($defaultCode, $this->storeManager->getStore()->getCode());
        $this->storeContextManager->resetContext();
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetContextFromSpecificationWithInvalidStore() : void
    {
        $specification = $this->specificationBuilder->build(['store' => 'invalid_store']);
        $this->expectException(NoSuchEntityException::class);
        $this->storeContextManager->setContextFromSpecification($specification);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/store.php
     */
    public function testSetContextFromSpecificationWithNotDefaultStore() : void
    {
        $storeCode = 'test';
        $this->assertStore($storeCode);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/multiple_websites_with_store_groups_stores.php
     */
    public function testSetContextFromSpecificationWithStoreFromDifferentWebsite() : void
    {
        $storeCode = 'second_store_view';
        $this->assertStore($storeCode);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/store.php
     */
    public function testResetContext() : void
    {
        $storeCode = 'test';
        $this->assertResetContext($storeCode);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/store.php
     */
    public function testResetContextWithEmptyStore() : void
    {
        $defaultStoreCode = 'default';
        $this->assertEquals($defaultStoreCode, $this->storeManager->getStore()->getCode());
        $specification = $this->specificationBuilder->build(['store' => null]);
        $this->storeContextManager->setContextFromSpecification($specification);
        $this->assertEquals($defaultStoreCode, $this->storeManager->getStore()->getCode());
        $this->storeContextManager->resetContext();
        $this->assertEquals($defaultStoreCode, $this->storeManager->getStore()->getCode());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/multiple_websites_with_store_groups_stores.php
     */
    public function testResetContextWithStoreFromDifferentWebsite() : void
    {
        $storeCode = 'second_store_view';
        $this->assertResetContext($storeCode);
    }

    /**
     * @param string $storeCode
     * @throws NoSuchEntityException
     */
    private function assertStore(string $storeCode) : void
    {
        $defaultStoreCode = 'default';
        $this->assertEquals($defaultStoreCode, $this->storeManager->getStore()->getCode());
        $specification = $this->specificationBuilder->build(['store' => $storeCode]);
        $this->storeContextManager->setContextFromSpecification($specification);
        $this->assertEquals($storeCode, $this->storeManager->getStore()->getCode());
        $this->storeContextManager->resetContext();
    }

    /**
     * @param string $storeCode
     * @param string $defaultStoreCode
     * @throws NoSuchEntityException
     */
    private function assertResetContext(string $storeCode, string $defaultStoreCode = 'default') : void
    {
        $this->assertEquals($defaultStoreCode, $this->storeManager->getStore()->getCode());
        $specification = $this->specificationBuilder->build(['store' => $storeCode]);
        $this->storeContextManager->setContextFromSpecification($specification);
        $this->assertEquals($storeCode, $this->storeManager->getStore()->getCode());
        $this->storeContextManager->resetContext();
        $this->assertEquals($defaultStoreCode, $this->storeManager->getStore()->getCode());
    }
}
