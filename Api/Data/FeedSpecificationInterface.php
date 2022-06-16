<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api\Data;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

interface FeedSpecificationInterface extends ExtensibleDataInterface
{
    /**
     *
     */
    const STORE_CODE = 'store_code';
    /**
     *
     */
    const HIERARCHY_SEPARATOR = 'hierarchy_separator';
    /**
     *
     */
    const MULTI_VALUED_SEPARATOR = 'multi_valued_separator';
    /**
     *
     */
    const INCLUDE_URL_HIERARCHY = 'include_url_hierarchy';
    /**
     *
     */
    const INCLUDE_MENU_CATEGORIES = 'include_menu_categories';
    /**
     *
     */
    const INCLUDE_JSON_CONFIG = 'include_json_config';
    /**
     *
     */
    const INCLUDE_CHILD_PRICES = 'include_child_prices';
    /**
     *
     */
    const INCLUDE_TIER_PRICES = 'include_tier_prices';
    /**
     *
     */
    const CUSTOMER_ID = 'customer_id';
    /**
     *
     */
    const CUSTOMER = 'customer';
    /**
     *
     */
    const CHILD_FIELDS = 'child_fields';
    /**
     *
     */
    const INCLUDE_OUT_OF_STOCK = 'include_out_of_stock';
    /**
     *
     */
    const IGNORE_FIELDS = 'ignore_fields';
    /**
     *
     */
    const FORMAT = 'format';
    /**
     *
     */
    const MEDIA_GALLERY_SPECIFICATION = 'media_gallery_specification';
    /**
     * @return string|null
     */
    public function getStoreCode() : ?string;

    /**
     * @param string $code
     * @return FeedSpecificationInterface
     */
    public function setStoreCode(string $code) : self;
    /**
     * @return string|null
     */
    public function getHierarchySeparator() : ?string;

    /**
     * @param string $separator
     * @return FeedSpecificationInterface
     */
    public function setHierarchySeparator(string $separator) : self;
    /**
     * @return string|null
     */
    public function getMultiValuedSeparator() : ?string;

    /**
     * @param string $separator
     * @return FeedSpecificationInterface
     */
    public function setMultiValuedSeparator(string $separator) : self;

    /**
     * @return bool|null
     */
    public function getIncludeUrlHierarchy() : ?bool;

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeUrlHierarchy(bool $flag) : self;

    /**
     * @return bool|null
     */
    public function getIncludeMenuCategories() : ?bool;

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeMenuCategories(bool $flag) : self;

    /**
     * @return bool|null
     */
    public function getIncludeJSONConfig() : ?bool;

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeJSONConfig(bool $flag) : self;

    /**
     * @return bool|null
     */
    public function getIncludeChildPrices() : ?bool;

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeChildPrices(bool $flag) : self;

    /**
     * @return bool|null
     */
    public function getIncludeTierPricing() : ?bool;

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeTierPricing(bool $flag) : self;

    /**
     * @return int|null
     */
    public function getCustomerId() : ?int;

    /**
     * @param int $id
     * @return FeedSpecificationInterface
     */
    public function setCustomerId(int $id) : self;

    /**
     * @return CustomerInterface|null
     */
    public function getCustomer() : ?CustomerInterface;

    /**
     * @param CustomerInterface $customer
     * @return FeedSpecificationInterface
     */
    public function setCustomer(CustomerInterface $customer) : self;

    /**
     * @return array
     */
    public function getChildFields() : array;

    /**
     * @param array $fields
     * @return FeedSpecificationInterface
     */
    public function setChildFields(array $fields) : self;

    /**
     * @return bool|null
     */
    public function getIncludeOutOfStock() : ?bool;

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeOutOfStock(bool $flag) : self;

    /**
     * @return array
     */
    public function getIgnoreFields() : array;

    /**
     * @param array $fields
     * @return FeedSpecificationInterface
     */
    public function setIgnoreFields(array $fields) : self;

    /**
     * @return string|null
     */
    public function getFormat() : ?string;

    /**
     * @param string $format
     * @return FeedSpecificationInterface
     */
    public function setFormat(string $format) : self;

    /**
     * @return MediaGallerySpecificationInterface|null
     */
    public function getMediaGallerySpecification() : ?MediaGallerySpecificationInterface;

    /**
     * @param MediaGallerySpecificationInterface $specification
     * @return FeedSpecificationInterface
     */
    public function setMediaGallerySpecification(MediaGallerySpecificationInterface $specification) : self;

    /**
     * @return \SearchSpring\Feed\Api\FeedSpecificationExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\SearchSpring\Feed\Api\FeedSpecificationExtensionInterface;

    /**
     * @param \SearchSpring\Feed\Api\FeedSpecificationExtensionInterface $extensionAttributes
     * @return FeedSpecificationInterface
     */
    public function setExtensionAttributes(
        \SearchSpring\Feed\Api\FeedSpecificationExtensionInterface $extensionAttributes
    ): self;
}