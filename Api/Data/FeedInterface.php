<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface FeedInterface extends ExtensibleDataInterface
{
    /**
     *
     */
    const ENTITY_ID = 'entity_id';
    /**
     *
     */
    const TASK_ID = 'task_id';
    /**
     *
     */
    const DIRECTORY_TYPE = 'directory_type';
    /**
     *
     */
    const FORMAT = 'format';
    /**
     *
     */
    const FILE_PATH = 'file_path';
    /**
     *
     */
    const FETCHED = 'fetched';
    /**
     *
     */
    const CREATED_AT = 'created_at';

    /**
     * @return mixed
     */
    public function getEntityId();

    /**
     * @param $id
     * @return mixed
     */
    public function setEntityId($id);

    /**
     * @return int|null
     */
    public function getTaskId() : ?int;

    /**
     * @param int $id
     * @return FeedInterface
     */
    public function setTaskId(int $id) : self;

    /**
     * @return string|null
     */
    public function getDirectoryType() : ?string;

    /**
     * @param string $type
     * @return FeedInterface
     */
    public function setDirectoryType(string $type) : self;

    /**
     * @return string|null
     */
    public function getFilePath() : ?string;

    /**
     * @param string $path
     * @return FeedInterface
     */
    public function setFilePath(string $path) : self;

    /**
     * @return bool|null
     */
    public function getFetched() : ?bool;

    /**
     * @param bool $flag
     * @return FeedInterface
     */
    public function setFetched(bool $flag) : self;

    /**
     * @return string|null
     */
    public function getCreatedAt() : ?string;

    /**
     * @param string $date
     * @return FeedInterface
     */
    public function setCreatedAt(string $date) : self;

    /**
     * @return string|null
     */
    public function getFormat() : ?string;

    /**
     * @param string $format
     * @return FeedInterface
     */
    public function setFormat(string $format) : self;

    /**
     * @return \SearchSpring\Feed\Api\Data\FeedExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\SearchSpring\Feed\Api\Data\FeedExtensionInterface;

    /**
     * @param \SearchSpring\Feed\Api\Data\FeedExtensionInterface $extensionAttributes
     * @return FeedInterface
     */
    public function setExtensionAttributes(
        \SearchSpring\Feed\Api\Data\FeedExtensionInterface $extensionAttributes
    ): self;
}
