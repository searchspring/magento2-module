<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider\Stock;

use SearchSpring\Feed\Exception\NoSuchEntityException;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\CompositeStockResolver;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\StockProviderInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Stock\StockResolverInterface;

class CompositeStockResolverTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->msiStockResolver = $this->createMock(StockResolverInterface::class);
        $this->legacyStockResolver = $this->createMock(StockResolverInterface::class);
        $resolvers = [
            'msi' => [
                'sortOrder' => 100,
                'objectInstance' => $this->msiStockResolver
            ],
            'legacy' => [
                'sortOrder' => 1000,
                'objectInstance' => $this->legacyStockResolver
            ]
        ];
        $this->compositeStockResolver = new CompositeStockResolver($resolvers);
    }

    public function testResolve()
    {
        $stockProviderInterfaceMock = $this->getMockForAbstractClass(StockProviderInterface::class);
        $this->msiStockResolver->expects($this->any())
            ->method('resolve')
            ->willReturn($stockProviderInterfaceMock);

        $this->assertSame($stockProviderInterfaceMock, $this->compositeStockResolver->resolve());
    }

    public function testResolveExceptionCase()
    {
        $this->msiStockResolver->expects($this->any())
            ->method('resolve')
            ->willThrowException(new NoSuchEntityException());
        $this->expectException(NoSuchEntityException::class);

        $this->compositeStockResolver->resolve();
    }
}
