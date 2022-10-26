<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Task\Validator;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Task\Validator\CustomerValidator;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerValidatorTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var CustomerValidator
     */
    private $customerValidator;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->customerValidator = $this->objectManager->get(CustomerValidator::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/customer.php
     *
     * @return void
     */
    public function testValidate() : void
    {
        $payload = ['customerId' => 1];
        $result = $this->customerValidator->validate($payload);
        $this->assertEmpty($result->getErrors());
        $this->assertTrue($result->isValid());
    }

    /**
     * @magentoAppIsolation enabled
     *
     * @return void
     */
    public function testValidateEmptyCustomer() : void
    {
        $result = $this->customerValidator->validate([]);
        $this->assertEmpty($result->getErrors());
        $this->assertTrue($result->isValid());
    }

    /**
     * @magentoAppIsolation enabled
     *
     * @return void
     */
    public function testValidateNotExistedCustomer() : void
    {
        $payload = ['customerId' => 100000];
        $result = $this->customerValidator->validate($payload);
        $this->assertNotEmpty($result->getErrors());
        $this->assertFalse($result->isValid());
    }
}
