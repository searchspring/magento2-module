<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\Data\MediaGallerySpecificationInterface;
use SearchSpring\Feed\Model\Feed\Collection\MediaGalleryProcessor;

class MediaGalleryProcessorTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->mediaGalleryProcessor = new MediaGalleryProcessor();
    }


    public function testProcess()
    {
        $mediaGallerySpecificationMock = $this->getMockForAbstractClass(MediaGallerySpecificationInterface::class);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getMediaGallerySpecification')
            ->willReturn($mediaGallerySpecificationMock);
        $mediaGallerySpecificationMock->expects($this->once())
            ->method('getIncludeMediaGallery')
            ->willReturn(null);

        $collectionMock = $this->createMock(Collection::class);

        $this->assertSame(
            $collectionMock,
            $this->mediaGalleryProcessor->processAfterLoad($collectionMock, $feedSpecificationMock)
        );
    }
}
