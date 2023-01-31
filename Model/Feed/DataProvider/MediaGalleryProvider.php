<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\Serializer\Json;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\Data\MediaGallerySpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class MediaGalleryProvider implements DataProviderInterface
{
    /**
     * @var Image
     */
    private $imageHelper;
    /**
     * @var Json
     */
    private $json;

    private $imageHelpers = [];

    /**
     * MediaGalleryProvider constructor.
     * @param Image $imageHelper
     * @param Json $json
     */
    public function __construct(
        Image $imageHelper,
        Json $json
    ) {
        $this->imageHelper = $imageHelper;
        $this->json = $json;
    }

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        foreach ($products as &$product) {
            $model = $product['product_model'] ?? null;
            if (!$model) {
                continue;
            }

            $product = array_merge($product, $this->getImages($model, $feedSpecification));
        }

        return $products;
    }

    /**
     * @param Product $product
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    private function getImages(Product $product, FeedSpecificationInterface $feedSpecification) : array
    {
        $mediaGallerySpecification = $feedSpecification->getMediaGallerySpecification();
        $ignoredFields = $feedSpecification->getIgnoreFields();
        $imageTypes = $mediaGallerySpecification->getImageTypes();
        $typeMap['cached_thumbnail'] = 'product_thumbnail_image';
        foreach ($imageTypes as $imageType) {
            $key = 'cached_' . $imageType;
            if (!in_array($key, $ignoredFields)) {
                $typeMap[$key] = $imageType;
            }
        }

        $result = [];
        foreach ($typeMap as $key => $value) {
            $result[$key] = $this->getImage($product, $value, $mediaGallerySpecification);
        }

        if ($mediaGallerySpecification->getIncludeMediaGallery()
            && !in_array('media_gallery_json', $ignoredFields)
        ) {
            $images = $product->getMediaGalleryImages();
            $mediaGallery = [];
            foreach($images as $image) {
                if($image->getMediaType() == 'image') {
                    $mediaGallery[] = [
                        'label' => $image->getLabel(),
                        'position' => $image->getPosition(),
                        'disabled' => $image->getDisabled(),
                        'image' => $this->getImage(
                            $product,
                            'product_thumbnail_image',
                            $mediaGallerySpecification,
                            $image->getFile()
                        )
                    ];
                }
            }

            $result['media_gallery_json'] = $this->json->serialize($mediaGallery);
        }

        return $result;
    }

    /**
     * @param Product $product
     * @param string $type
     * @param MediaGallerySpecificationInterface $mediaGallerySpecification
     * @param string|null $file
     * @return string
     */
    private function getImage(
        Product $product,
        string $type,
        MediaGallerySpecificationInterface $mediaGallerySpecification,
        string $file = null
    ) : string {
        $imageHelper = $this->imageHelper->init($product, $type);

        if ($file) {
            $imageHelper->setImageFile($file);
        }

        if($mediaGallerySpecification->getKeepAspectRatio()) {
            $resizedImage = $imageHelper->constrainOnly(true)
                ->keepAspectRatio(true)
                ->keepTransparency(true)
                ->keepFrame(false)
                ->resize($mediaGallerySpecification->getThumbWidth(), $mediaGallerySpecification->getThumbHeight())
                ->getUrl();
        } else {
            $resizedImage = $imageHelper->resize(
                $mediaGallerySpecification->getThumbWidth(),
                $mediaGallerySpecification->getThumbHeight()
            )->getUrl();
        }

        return $resizedImage;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->imageHelpers = [];
    }

    /**
     *
     */
    public function resetAfterFetchItems(): void
    {
        // do nothing
    }
}
