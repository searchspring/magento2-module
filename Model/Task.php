<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use SearchSpring\Feed\Api\Data\TaskErrorInterface;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Model\ResourceModel\Task as TaskResource;

class Task extends AbstractExtensibleModel implements TaskInterface
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(TaskResource::class);
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
     * @return TaskInterface
     */
    public function setEntityId($id) : TaskInterface
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @param string $type
     * @return TaskInterface
     */
    public function setType(string $type): TaskInterface
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param string $status
     * @return TaskInterface
     */
    public function setStatus(string $status): TaskInterface
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->getData(self::PAYLOAD) ?? [];
    }

    /**
     * @param array $payload
     * @return TaskInterface
     */
    public function setPayload(array $payload): TaskInterface
    {
        return $this->setData(self::PAYLOAD, $payload);
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
     * @return TaskInterface
     */
    public function setCreatedAt(string $date): TaskInterface
    {
        return $this->setData(self::CREATED_AT, $date);
    }

    /**
     * @return string|null
     */
    public function getStartedAt(): ?string
    {
        return $this->getData(self::STARTED_AT);
    }

    /**
     * @param string $date
     * @return TaskInterface
     */
    public function setStartedAt(string $date): TaskInterface
    {
        return $this->setData(self::STARTED_AT, $date);
    }

    /**
     * @return string|null
     */
    public function getEndedAt(): ?string
    {
        return $this->getData(self::ENDED_AT);
    }

    /**
     * @param string $date
     * @return TaskInterface
     */
    public function setEndedAt(string $date): TaskInterface
    {
        return $this->setData(self::ENDED_AT, $date);
    }

    /**
     * @return TaskErrorInterface|null
     */
    public function getError(): ?TaskErrorInterface
    {
        return $this->getData(self::ERROR);
    }

    /**
     * @param TaskErrorInterface $error
     * @return TaskInterface
     */
    public function setError(TaskErrorInterface $error): TaskInterface
    {
        return $this->setData(self::ERROR, $error);
    }

    /**
     * @return \SearchSpring\Feed\Api\TastExtensionInterface|null
     */
    public function getExtensionAttributes(): ?\SearchSpring\Feed\Api\TastExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \SearchSpring\Feed\Api\TastExtensionInterface $extensionAttributes
     * @return TaskInterface
     */
    public function setExtensionAttributes(\SearchSpring\Feed\Api\TastExtensionInterface $extensionAttributes): TaskInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
