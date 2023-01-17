<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Context;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Context\CustomerContextManager;

class CustomerContextManagerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->sessionMock = $this->createMock(Session::class);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->customerContextManager = new CustomerContextManager(
            $this->sessionMock,
            $this->customerRepositoryMock
        );
    }

    public function testSetContextFromSpecification()
    {
        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn(1);

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($customerMock);
        $this->sessionMock->expects($this->once())
            ->method('setCustomerDataAsLoggedIn')
            ->with($customerMock)
            ->willReturnSelf();

        $this->customerContextManager->setContextFromSpecification($feedSpecificationMock);
    }

    public function testResetContext()
    {
        $this->sessionMock->expects($this->once())
            ->method('logout')
            ->willReturnSelf();

        $this->customerContextManager->resetContext();
    }
}
