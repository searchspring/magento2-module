<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Model\ResourceModel\Task\Error\DeleteErrors;
use SearchSpring\Feed\Model\ResourceModel\Task\Error\SaveError;
use SearchSpring\Feed\Model\Task as TaskModel;

class Task extends AbstractDb
{
    const TABLE = 'searchspring_task';
    const ERROR_TABLE = 'searchspring_task_error';
    /**
     * @var DeleteErrors
     */
    private $deleteErrors;
    /**
     * @var SaveError
     */
    private $saveError;

    /**
     * Task constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param Snapshot $entitySnapshot
     * @param RelationComposite $entityRelationComposite
     * @param DeleteErrors $deleteErrors
     * @param SaveError $saveError
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        DeleteErrors $deleteErrors,
        SaveError $saveError,
        $connectionName = null
    ) {
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
        $this->deleteErrors = $deleteErrors;
        $this->saveError = $saveError;
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, TaskInterface::ENTITY_ID);
    }

    /**
     * @param TaskModel $object
     * @return AbstractDb
     * @throws \Exception
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getError()) {
            $this->saveError->execute($object->getEntityId(), $object->getError());
        } else {
            $this->deleteErrors->execute([$object->getEntityId()]);
        }

        return parent::_afterSave($object);
    }
}
