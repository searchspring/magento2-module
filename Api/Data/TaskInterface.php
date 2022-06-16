<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface TaskInterface extends ExtensibleDataInterface
{
    /**
     *
     */
    const ENTITY_ID = 'entity_id';
    /**
     *
     */
    const TYPE = 'type';
    /**
     *
     */
    const STATUS = 'status';
    /**
     *
     */
    const PAYLOAD = 'payload';
    /**
     *
     */
    const ERROR = 'error';
    /**
     *
     */
    const CREATED_AT = 'created_at';
    /**
     *
     */
    const STARTED_AT = 'started_at';
    /**
     *
     */
    const ENDED_AT = 'ended_at';

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
     * @return string|null
     */
    public function getType() : ?string;

    /**
     * @param string $type
     * @return TaskInterface
     */
    public function setType(string $type) : self;

    /**
     * @return string|null
     */
    public function getStatus() : ?string;

    /**
     * @param string $status
     * @return TaskInterface
     */
    public function setStatus(string $status) : self;

    /**
     * @return array
     */
    public function getPayload() : array;

    /**
     * @param array $payload
     * @return TaskInterface
     */
    public function setPayload(array $payload) : self;

    /**
     * @return string|null
     */
    public function getCreatedAt() : ?string;

    /**
     * @param string $date
     * @return TaskInterface
     */
    public function setCreatedAt(string $date) : self;

    /**
     * @return string|null
     */
    public function getStartedAt() : ?string;

    /**
     * @param string $date
     * @return TaskInterface
     */
    public function setStartedAt(string $date) : self;

    /**
     * @return string|null
     */
    public function getEndedAt() : ?string;

    /**
     * @param string $date
     * @return TaskInterface
     */
    public function setEndedAt(string $date) : self;

    /**
     * @return TaskErrorInterface|null
     */
    public function getError() : ?TaskErrorInterface;

    /**
     * @param TaskErrorInterface $error
     * @return TaskInterface
     */
    public function setError(TaskErrorInterface $error) : self;

    /**
     * @return \SearchSpring\Feed\Api\TastExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\SearchSpring\Feed\Api\TastExtensionInterface;

    /**
     * @param \SearchSpring\Feed\Api\TastExtensionInterface $extensionAttributes
     * @return TaskInterface
     */
    public function setExtensionAttributes(
        \SearchSpring\Feed\Api\TastExtensionInterface $extensionAttributes
    ): self;
}
