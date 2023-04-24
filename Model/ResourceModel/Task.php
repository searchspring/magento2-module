<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Serialize\SerializerInterface;
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
     * @param SerializerInterface $serializer
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        DeleteErrors $deleteErrors,
        SaveError $saveError,
        SerializerInterface $serializer,
        $connectionName = null
    ) {
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $connectionName);
        $this->deleteErrors = $deleteErrors;
        $this->saveError = $saveError;
        $this->serializer = $serializer;
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
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $payload = $object->getPayload();
        $object->setData(TaskInterface::PAYLOAD, $this->serializer->serialize($payload));
        return parent::_beforeSave($object);
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
