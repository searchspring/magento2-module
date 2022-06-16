<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface MediaGallerySpecificationInterface extends ExtensibleDataInterface
{
    const THUMB_WIDTH = 'thumb_width';
    const THUMB_HEIGHT = 'thumb_height';
    const KEEP_ASPECT_RATIO = 'keep_aspect_ratio';
    const IMAGE_TYPES = 'image_types';
    const INCLUDE_MEDIA_GALLERY = 'include_media_gallery';
    /**
     * @return int
     */
    public function getThumbWidth() : ?int;

    /**
     * @param int $width
     * @return MediaGallerySpecificationInterface
     */
    public function setThumbWidth(int $width) : self;

    /**
     * @return int
     */
    public function getThumbHeight() : ?int;

    /**
     * @param int $height
     * @return MediaGallerySpecificationInterface
     */
    public function setThumbHeight(int $height) : self;

    /**
     * @return bool
     */
    public function getKeepAspectRatio() : ?bool;

    /**
     * @param bool $flag
     * @return MediaGallerySpecificationInterface
     */
    public function setKeepAspectRatio(bool $flag) : self;

    /**
     * @return array
     */
    public function getImageTypes() : array;

    /**
     * @param array $types
     * @return MediaGallerySpecificationInterface
     */
    public function setImageTypes(array $types) : self;

    /**
     * @return bool
     */
    public function getIncludeMediaGallery() : ?bool;

    /**
     * @param bool $flag
     * @return MediaGallerySpecificationInterface
     */
    public function setIncludeMediaGallery(bool $flag) : self;


    /**
     * @return \SearchSpring\Feed\Api\MediaGallerySpecificationExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\SearchSpring\Feed\Api\MediaGallerySpecificationExtensionInterface;

    /**
     * @param \SearchSpring\Feed\Api\MediaGallerySpecificationExtensionInterface $extensionAttributes
     * @return MediaGallerySpecificationInterface
     */
    public function setExtensionAttributes(
        \SearchSpring\Feed\Api\MediaGallerySpecificationExtensionInterface $extensionAttributes
    ): self;
}
