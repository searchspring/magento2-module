<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterfaceFactory;
use SearchSpring\Feed\Api\Data\MediaGallerySpecificationInterface;
use SearchSpring\Feed\Api\Data\MediaGallerySpecificationInterfaceFactory;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Model\Feed\SpecificationBuilder;

class SpecificationBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FeedSpecificationInterfaceFactory
     */
    private $feedSpecificationFactoryMock;

    /**
     * @var MediaGallerySpecificationInterfaceFactory
     */
    private $mediaGallerySpecificationFactoryMock;

    /**
     * @var array
     */
    private $defaultValues = [
        'feed' => [
            FeedSpecificationInterface::STORE_CODE => 'default',
            FeedSpecificationInterface::HIERARCHY_SEPARATOR => '/',
            FeedSpecificationInterface::MULTI_VALUED_SEPARATOR => '|',
            FeedSpecificationInterface::INCLUDE_URL_HIERARCHY => false,
            FeedSpecificationInterface::INCLUDE_MENU_CATEGORIES => false,
            FeedSpecificationInterface::INCLUDE_JSON_CONFIG => false,
            FeedSpecificationInterface::INCLUDE_CHILD_PRICES => false,
            FeedSpecificationInterface::INCLUDE_TIER_PRICES => false,
            FeedSpecificationInterface::CUSTOMER_ID => null,
            FeedSpecificationInterface::CHILD_FIELDS => [],
            FeedSpecificationInterface::INCLUDE_OUT_OF_STOCK => false,
            FeedSpecificationInterface::IGNORE_FIELDS => [],
            FeedSpecificationInterface::FORMAT => MetadataInterface::FORMAT_CSV,
        ],
        'media_gallery' => [
            MediaGallerySpecificationInterface::THUMB_WIDTH => 250,
            MediaGallerySpecificationInterface::THUMB_HEIGHT => 250,
            MediaGallerySpecificationInterface::KEEP_ASPECT_RATIO => 1,
            MediaGallerySpecificationInterface::IMAGE_TYPES => [],
            MediaGallerySpecificationInterface::INCLUDE_MEDIA_GALLERY => 0
        ]
    ];

    public function setUp(): void
    {
        $this->feedSpecificationFactoryMock =
            $this->createMock(FeedSpecificationInterfaceFactory::class);
        $this->mediaGallerySpecificationFactoryMock =
            $this->createMock(MediaGallerySpecificationInterfaceFactory::class);
        $this->specificationBuilder = new SpecificationBuilder(
            $this->feedSpecificationFactoryMock,
            $this->mediaGallerySpecificationFactoryMock
        );
    }

    public function testBuild()
    {
        $mediaGallerySpecificationMock = $this->getMockForAbstractClass(MediaGallerySpecificationInterface::class);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $this->feedSpecificationFactoryMock->expects($this->once())
            ->method('create')
            ->with(['data' => $this->defaultValues['feed']])
            ->willReturn($feedSpecificationMock);
        $this->mediaGallerySpecificationFactoryMock->expects($this->once())
            ->method('create')
            ->with(['data' => $this->defaultValues['media_gallery']])
            ->willReturn($mediaGallerySpecificationMock);
        $feedSpecificationMock->expects($this->once())
            ->method('setMediaGallerySpecification')
            ->with($mediaGallerySpecificationMock)
            ->willReturnSelf();

        $this->assertSame($feedSpecificationMock, $this->specificationBuilder->build([]));
    }
}