<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\Data\MediaGallerySpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\MediaGalleryProcessor;

class MediaGalleryProcessorTest extends \PHPUnit\Framework\TestCase
{
    private $mediaGalleryProcessor;

    public function setUp(): void
    {
        $this->mediaGalleryProcessor = new MediaGalleryProcessor();
    }


    public function testProcessAfterLoad()
    {
        $mediaGallerySpecificationMock = $this->getMockForAbstractClass(MediaGallerySpecificationInterface::class);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getMediaGallerySpecification')
            ->willReturn($mediaGallerySpecificationMock);
        $mediaGallerySpecificationMock->expects($this->once())
            ->method('getIncludeMediaGallery')
            ->willReturn(true);

        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('addMediaGalleryData')
            ->willReturnSelf();

        $this->assertSame(
            $collectionMock,
            $this->mediaGalleryProcessor->processAfterLoad($collectionMock, $feedSpecificationMock)
        );
    }

    public function testProcessAfterFetchItems()
    {
        $mediaGallerySpecificationMock = $this->getMockForAbstractClass(MediaGallerySpecificationInterface::class);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getMediaGallerySpecification')
            ->willReturn($mediaGallerySpecificationMock);
        $mediaGallerySpecificationMock->expects($this->once())
            ->method('getIncludeMediaGallery')
            ->willReturn(true);

        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('setFlag')
            ->with('media_gallery_added', false)
            ->willReturnSelf();

        $this->assertSame(
            $collectionMock,
            $this->mediaGalleryProcessor->processAfterFetchItems($collectionMock, $feedSpecificationMock)
        );
    }
}
