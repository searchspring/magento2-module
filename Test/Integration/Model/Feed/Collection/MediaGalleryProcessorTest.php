<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Model\Feed\Collection\MediaGalleryProcessor;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MediaGalleryProcessorTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var MediaGalleryProcessor
     */
    private $mediaGalleryProcessor;
    /**
     * @var SpecificationBuilderInterface
     */
    private $specificationBuilder;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->mediaGalleryProcessor = $this->objectManager->get(MediaGalleryProcessor::class);
        $this->specificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/product_simple_with_media_gallery_entries.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @throws LocalizedException
     */
    public function testProcessAfterLoad() : void
    {
        $specification = $this->specificationBuilder->build(['includeMediaGallery' => 1]);
        $collection = $this->getCollection();
        $this->mediaGalleryProcessor->processAfterLoad($collection, $specification);
        $this->assertTrue($collection->getFlag('media_gallery_added'));
        foreach ($collection as $item) {
            if (in_array($item->getSku(), ['searchspring_simple_1', 'searchspring_simple_2'])) {
                // check that product without media gallery still doesn't have media gallery like from other products
                $this->assertEmpty($item->getMediaGallery('images'));
                $this->assertEmpty($item->getMediaGallery('values'));
            } else {
                $this->assertNotEmpty($item->getMediaGallery());
            }
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/product_simple_with_media_gallery_entries.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     */
    public function testProcessAfterLoadWithIncludeMediaGalleryIsFalse() : void
    {
        $specification = $this->specificationBuilder->build(['includeMediaGallery' => 0]);
        $collection = $this->getCollection();
        $this->mediaGalleryProcessor->processAfterLoad($collection, $specification);
        $this->assertNull($collection->getFlag('media_gallery_added'));
        foreach ($collection as $item) {
            $this->assertNull($item->getMediaGallery());
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/product_simple_with_media_gallery_entries.php
     * @magentoDataFixture SearchSpring_Feed::Test/_files/simple_products.php
     * @throws LocalizedException
     */
    public function testProcessAfterFetchItems() : void
    {
        $specification = $this->specificationBuilder->build(['includeMediaGallery' => 1]);
        $collection = $this->getCollection();
        $this->mediaGalleryProcessor->processAfterLoad($collection, $specification);
        $this->assertTrue($collection->getFlag('media_gallery_added'));
        foreach ($collection as $item) {
            if (in_array($item->getSku(), ['searchspring_simple_1', 'searchspring_simple_2'])) {
                // check that product without media gallery still doesn't have media gallery like from other products
                $this->assertEmpty($item->getMediaGallery('images'));
                $this->assertEmpty($item->getMediaGallery('values'));
            } else {
                $this->assertNotEmpty($item->getMediaGallery());
            }
        }

        $collection->clear();
        $this->assertTrue($collection->getFlag('media_gallery_added'));
        $this->mediaGalleryProcessor->processAfterFetchItems($collection, $specification);
        $this->assertFalse($collection->getFlag('media_gallery_added'));
    }

    /**
     * @return Collection
     */
    private function getCollection() : Collection
    {
        return $this->objectManager->create(Collection::class);
    }
}
