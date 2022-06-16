<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Model\ResourceModel\Feed as FeedResource;

class Feed extends AbstractExtensibleModel implements FeedInterface
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(FeedResource::class);
    }

    /**
     * @return int|null
     */
    public function getEntityId() : ?int
    {
        return !is_null($this->getData(self::ENTITY_ID))
            ? (int) $this->getData(self::ENTITY_ID)
            : null;
    }

    /**
     * @param int $id
     * @return FeedInterface
     */
    public function setEntityId($id) : FeedInterface
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @return int|null
     */
    public function getTaskId(): ?int
    {
        return !is_null($this->getData(self::TASK_ID))
            ? (int) $this->getData(self::TASK_ID)
            : null;
    }

    /**
     * @param int $id
     * @return FeedInterface
     */
    public function setTaskId(int $id): FeedInterface
    {
        return $this->setData(self::TASK_ID, $id);
    }

    /**
     * @return string|null
     */
    public function getDirectoryType(): ?string
    {
        return $this->getData(self::DIRECTORY_TYPE);
    }

    /**
     * @param string $type
     * @return FeedInterface
     */
    public function setDirectoryType(string $type): FeedInterface
    {
        return $this->setData(self::DIRECTORY_TYPE, $type);
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->getData(self::FILE_NAME);
    }

    /**
     * @param string $name
     * @return FeedInterface
     */
    public function setFileName(string $name): FeedInterface
    {
        return $this->setData(self::FILE_NAME, $name);
    }

    /**
     * @return bool|null
     */
    public function getFetched(): ?bool
    {
        return !is_null($this->getData(self::FETCHED))
            ? (bool) $this->getData(self::FETCHED)
            : null;
    }

    /**
     * @param bool $flag
     * @return FeedInterface
     */
    public function setFetched(bool $flag): FeedInterface
    {
        return $this->setData(self::FETCHED, $flag);
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $date
     * @return FeedInterface
     */
    public function setCreatedAt(string $date): FeedInterface
    {
        return $this->setData(self::CREATED_AT, $date);
    }

    /**
     * @return \SearchSpring\Feed\Api\FeedExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\SearchSpring\Feed\Api\FeedExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \SearchSpring\Feed\Api\FeedExtensionInterface $extensionAttributes
     * @return FeedInterface
     */
    public function setExtensionAttributes(\SearchSpring\Feed\Api\FeedExtensionInterface $extensionAttributes): FeedInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
