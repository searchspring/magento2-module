<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Task\Validator;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Task\Validator\StoreValidator;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StoreValidatorTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var StoreValidator
     */
    private $storeValidator;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->storeValidator = $this->objectManager->get(StoreValidator::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Store/_files/store.php
     */
    public function testValidate() : void
    {
        $payload = ['store' => 'test'];
        $result = $this->storeValidator->validate($payload);
        $this->assertEmpty($result->getErrors());
        $this->assertTrue($result->isValid());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testValidateWithEmptyStore() : void
    {
        $payload = [];
        $result = $this->storeValidator->validate($payload);
        $this->assertEmpty($result->getErrors());
        $this->assertTrue($result->isValid());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testValidateWithInvalidStoreCode() : void
    {
        $storeCode = 'test_invalid';
        $payload = ['store' => $storeCode];
        $result = $this->storeValidator->validate($payload);
        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $error = array_pop($result->getErrors());
        $this->assertEquals((string) __('Store "%1" doesn\'t exist', $storeCode), $error);
    }
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Store/_files/inactive_store.php
     */
    public function testValidateWithDeactivatedStoreCode() : void
    {
        $storeCode = 'inactive_store';
        $payload = ['store' => $storeCode];
        $result = $this->storeValidator->validate($payload);
        $this->assertFalse($result->isValid());
        $this->assertNotEmpty($result->getErrors());
        $error = array_pop($result->getErrors());
        $this->assertEquals((string) __('Store "%1" is not active', $storeCode), $error);
    }
}
