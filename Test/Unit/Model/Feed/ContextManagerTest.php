<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed;

use SearchSpring\Feed\Model\Feed\Context\CustomerContextManager;
use SearchSpring\Feed\Model\Feed\Context\StoreContextManager;
use SearchSpring\Feed\Model\Feed\ContextManager;
use SearchSpring\Feed\Model\Feed\Specification\Feed;

class ContextManagerTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->customerContextManagerMock = $this->createMock(CustomerContextManager::class);
        $this->storeContextManagerMock = $this->createMock(StoreContextManager::class);
        $processors = [
            $this->customerContextManagerMock,
            $this->storeContextManagerMock
        ];
        $this->contextManager = new ContextManager($processors);
    }

    public function testSetContextFromSpecification()
    {
        $feedSpecificationMock = $this->createMock(Feed::class);
        $this->customerContextManagerMock->expects($this->once())
            ->method('setContextFromSpecification')
            ->with($feedSpecificationMock);
        $this->storeContextManagerMock->expects($this->once())
            ->method('setContextFromSpecification')
            ->with($feedSpecificationMock);

        $this->contextManager->setContextFromSpecification($feedSpecificationMock);
    }

    public function testResetContext()
    {
        $this->customerContextManagerMock->expects($this->once())
            ->method('resetContext');
        $this->storeContextManagerMock->expects($this->once())
            ->method('resetContext');

        $this->contextManager->resetContext();
    }
}
