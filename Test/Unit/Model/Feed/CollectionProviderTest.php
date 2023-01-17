<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\CollectionProvider;
use SearchSpring\Feed\Model\Feed\Collection\StatusModifier;
use SearchSpring\Feed\Model\Feed\Collection\StoreModifier;
use SearchSpring\Feed\Model\Feed\Collection\VisibilityModifier;
use SearchSpring\Feed\Model\Feed\Collection\AttributesModifier;
use SearchSpring\Feed\Model\Feed\Collection\PricesModifier;
use SearchSpring\Feed\Model\Feed\Collection\StockModifier;

class CollectionProviderTest extends \PHPUnit\Framework\TestCase
{
    private $storeModifierMock;

    private $statusModifierMock;

    private $visibilityModifierMock;

    private $stockModifierMock;

    private $attributesModifierMock;

    private $pricesModifierMock;

    public function setUp(): void
    {
        $this->storeModifierMock = $this->createMock(StoreModifier::class);
        $this->statusModifierMock = $this->createMock(StatusModifier::class);
        $this->visibilityModifierMock = $this->createMock(VisibilityModifier::class);
        $this->stockModifierMock = $this->createMock(StockModifier::class);
        $this->attributesModifierMock = $this->createMock(AttributesModifier::class);
        $this->pricesModifierMock = $this->createMock(PricesModifier::class);

        $modifiers = [
            'store' => [
                'objectInstance' => $this->storeModifierMock,
                'sortOrder' => 100
            ],
            'status' => [
                'objectInstance' => $this->statusModifierMock,
                'sortOrder' => 200
            ],
            'visibility' => [
                'objectInstance' => $this->visibilityModifierMock,
                'sortOrder' => 300
            ],
            'stock' => [
                'objectInstance' => $this->stockModifierMock,
                'sortOrder' => 400
            ],
            'attributes' => [
                'objectInstance' => $this->attributesModifierMock,
                'sortOrder' => 500
            ],
            'price' => [
                'objectInstance' => $this->pricesModifierMock,
                'sortOrder' => 600
            ]
        ];
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->collectionProvider = new CollectionProvider(
            $this->collectionFactoryMock,
            $modifiers
        );
    }

    public function testGetCollection()
    {
        $collectionMock = $this->createMock(Collection::class);
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $this->storeModifierMock->expects($this->once())
            ->method('modify')
            ->with($collectionMock, $feedSpecificationMock)
            ->willReturn($collectionMock);
        $this->statusModifierMock->expects($this->once())
            ->method('modify')
            ->with($collectionMock, $feedSpecificationMock)
            ->willReturn($collectionMock);
        $this->visibilityModifierMock->expects($this->once())
            ->method('modify')
            ->with($collectionMock, $feedSpecificationMock)
            ->willReturn($collectionMock);
        $this->stockModifierMock->expects($this->once())
            ->method('modify')
            ->with($collectionMock, $feedSpecificationMock)
            ->willReturn($collectionMock);
        $this->attributesModifierMock->expects($this->once())
            ->method('modify')
            ->with($collectionMock, $feedSpecificationMock)
            ->willReturn($collectionMock);
        $this->pricesModifierMock->expects($this->once())
            ->method('modify')
            ->with($collectionMock, $feedSpecificationMock)
            ->willReturn($collectionMock);

        $this->assertSame(
            $collectionMock,
            $this->collectionProvider->getCollection($feedSpecificationMock)
        );
    }
}
