<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Specification;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\AbstractExtensibleObject;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\Data\MediaGallerySpecificationInterface;

class Feed extends AbstractExtensibleObject implements FeedSpecificationInterface
{

    /**
     * @return string|null
     */
    public function getStoreCode(): ?string
    {
        return $this->_get(self::STORE_CODE);
    }

    /**
     * @param string $code
     * @return FeedSpecificationInterface
     */
    public function setStoreCode(string $code): FeedSpecificationInterface
    {
        return $this->setData(self::STORE_CODE, $code);
    }

    /**
     * @return string|null
     */
    public function getHierarchySeparator(): ?string
    {
        return $this->_get(self::HIERARCHY_SEPARATOR);
    }

    /**
     * @param string $separator
     * @return FeedSpecificationInterface
     */
    public function setHierarchySeparator(string $separator): FeedSpecificationInterface
    {
        return $this->setData(self::HIERARCHY_SEPARATOR, $separator);
    }

    /**
     * @return string|null
     */
    public function getMultiValuedSeparator(): ?string
    {
        return $this->_get(self::MULTI_VALUED_SEPARATOR);
    }

    /**
     * @param string $separator
     * @return FeedSpecificationInterface
     */
    public function setMultiValuedSeparator(string $separator): FeedSpecificationInterface
    {
        return $this->setData(self::MULTI_VALUED_SEPARATOR, $separator);
    }

    /**
     * @return bool|null
     */
    public function getIncludeUrlHierarchy(): ?bool
    {
        return !is_null($this->_get(self::INCLUDE_URL_HIERARCHY))
            ? (bool) $this->_get(self::INCLUDE_URL_HIERARCHY)
            : null;
    }

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeUrlHierarchy(bool $flag): FeedSpecificationInterface
    {
        return $this->setData(self::INCLUDE_URL_HIERARCHY, $flag);
    }

    /**
     * @return bool|null
     */
    public function getIncludeMenuCategories(): ?bool
    {
        return !is_null($this->_get(self::INCLUDE_MENU_CATEGORIES))
            ? (bool) $this->_get(self::INCLUDE_MENU_CATEGORIES)
            : null;
    }

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeMenuCategories(bool $flag): FeedSpecificationInterface
    {
        return $this->setData(self::INCLUDE_MENU_CATEGORIES, $flag);
    }

    /**
     * @return bool|null
     */
    public function getIncludeJSONConfig(): ?bool
    {
        return !is_null($this->_get(self::INCLUDE_JSON_CONFIG))
            ? (bool) $this->_get(self::INCLUDE_JSON_CONFIG)
            : null;
    }

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeJSONConfig(bool $flag): FeedSpecificationInterface
    {
        return $this->setData(self::INCLUDE_JSON_CONFIG, $flag);
    }

    /**
     * @return bool|null
     */
    public function getIncludeChildPrices(): ?bool
    {
        return !is_null($this->_get(self::INCLUDE_CHILD_PRICES))
            ? (bool) $this->_get(self::INCLUDE_CHILD_PRICES)
            : null;
    }

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeChildPrices(bool $flag): FeedSpecificationInterface
    {
        return $this->setData(self::INCLUDE_CHILD_PRICES, $flag);
    }

    /**
     * @return bool|null
     */
    public function getIncludeTierPricing(): ?bool
    {
        return !is_null($this->_get(self::INCLUDE_TIER_PRICES))
            ? (bool) $this->_get(self::INCLUDE_TIER_PRICES)
            : null;
    }

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeTierPricing(bool $flag): FeedSpecificationInterface
    {
        return $this->setData(self::INCLUDE_TIER_PRICES, $flag);
    }

    /**
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return !is_null($this->_get(self::CUSTOMER_ID))
            ? (int) $this->_get(self::CUSTOMER_ID)
            : null;
    }

    /**
     * @param int $id
     * @return FeedSpecificationInterface
     */
    public function setCustomerId(int $id): FeedSpecificationInterface
    {
        return $this->setData(self::CUSTOMER_ID, $id);
    }

    /**
     * @return CustomerInterface|null
     */
    public function getCustomer(): ?CustomerInterface
    {
        return $this->_get(self::CUSTOMER);
    }

    /**
     * @param CustomerInterface $customer
     * @return FeedSpecificationInterface
     */
    public function setCustomer(CustomerInterface $customer): FeedSpecificationInterface
    {
        return $this->setData(self::CUSTOMER, $customer);
    }

    /**
     * @return array
     */
    public function getChildFields(): array
    {
        return $this->_get(self::CHILD_FIELDS) ?? [];
    }

    /**
     * @param array $fields
     * @return FeedSpecificationInterface
     */
    public function setChildFields(array $fields): FeedSpecificationInterface
    {
        return $this->setData(self::CHILD_FIELDS, $fields);
    }

    /**
     * @return bool|null
     */
    public function getIncludeOutOfStock(): ?bool
    {
        return !is_null($this->_get(self::INCLUDE_OUT_OF_STOCK))
            ? (bool) $this->_get(self::INCLUDE_OUT_OF_STOCK)
            : null;
    }

    /**
     * @param bool $flag
     * @return FeedSpecificationInterface
     */
    public function setIncludeOutOfStock(bool $flag): FeedSpecificationInterface
    {
        return $this->setData(self::INCLUDE_OUT_OF_STOCK, $flag);
    }

    /**
     * @return array
     */
    public function getIgnoreFields(): array
    {
        return $this->_get(self::IGNORE_FIELDS) ?? [];
    }

    /**
     * @param array $fields
     * @return FeedSpecificationInterface
     */
    public function setIgnoreFields(array $fields): FeedSpecificationInterface
    {
        return $this->setData(self::IGNORE_FIELDS, $fields);
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->_get(self::FORMAT);
    }

    /**
     * @param string $format
     * @return FeedSpecificationInterface
     */
    public function setFormat(string $format): FeedSpecificationInterface
    {
        return $this->setData(self::FORMAT, $format);
    }

    /**
     * @return MediaGallerySpecificationInterface|null
     */
    public function getMediaGallerySpecification(): ?MediaGallerySpecificationInterface
    {
        return $this->_get(self::MEDIA_GALLERY_SPECIFICATION);
    }

    /**
     * @param MediaGallerySpecificationInterface $specification
     * @return FeedSpecificationInterface
     */
    public function setMediaGallerySpecification(MediaGallerySpecificationInterface $specification): FeedSpecificationInterface
    {
        return $this->setData(self::MEDIA_GALLERY_SPECIFICATION, $specification);
    }

    /**
     * @return \SearchSpring\Feed\Api\FeedSpecificationExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\SearchSpring\Feed\Api\FeedSpecificationExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \SearchSpring\Feed\Api\FeedSpecificationExtensionInterface $extensionAttributes
     * @return FeedSpecificationInterface
     */
    public function setExtensionAttributes(\SearchSpring\Feed\Api\FeedSpecificationExtensionInterface $extensionAttributes): FeedSpecificationInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
