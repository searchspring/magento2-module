<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use SearchSpring\Feed\Api\Data\TaskErrorInterface;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\TastExtensionInterface;
use SearchSpring\Feed\Model\ResourceModel\Task as TaskResource;
use SearchSpring\Feed\Model\ResourceModel\Task\Error\LoadErrors;

class Task extends AbstractExtensibleModel implements TaskInterface
{
    /**
     * @var DateTime
     */
    private $dateTime;
    /**
     * @var LoadErrors
     */
    private $loadErrors;

    /**
     * Task constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param DateTime $dateTime
     * @param LoadErrors $loadErrors
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        DateTime $dateTime,
        LoadErrors $loadErrors,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection, $data);
        $this->dateTime = $dateTime;
        $this->loadErrors = $loadErrors;
    }

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
     * @return TastExtensionInterface|null
     */
    public function getExtensionAttributes(): ?TastExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param TastExtensionInterface $extensionAttributes
     * @return TaskInterface
     */
    public function setExtensionAttributes(TastExtensionInterface $extensionAttributes): TaskInterface
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @return AbstractExtensibleModel
     */
    public function beforeSave()
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt($this->dateTime->gmtDate());
        }
        return parent::beforeSave();
    }

    /**
     * @return AbstractExtensibleModel
     */
    public function afterLoad()
    {
        $this->loadError();
        return parent::afterLoad();
    }

    /**
     *
     */
    private function loadError() : void
    {
        if (!$this->getEntityId()) {
            return;
        }

        $entityId = $this->getEntityId();
        $errors = $this->loadErrors->execute([$entityId]);
        if (empty($errors)) {
            return;
        }

        $error = current($errors);
        $this->setError($error);
    }
}
