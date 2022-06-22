<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\ResourceModel\Task;

use Exception;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection as AbstractCollection;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Model\ResourceModel\Task as TaskResource;
use SearchSpring\Feed\Model\ResourceModel\Task\Error\LoadErrors;
use SearchSpring\Feed\Model\Task;

class Collection extends AbstractCollection
{
    /**
     * @var LoadErrors
     */
    private $loadErrors;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Snapshot $entitySnapshot
     * @param LoadErrors $loadErrors
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Snapshot $entitySnapshot,
        LoadErrors $loadErrors,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $entitySnapshot, $connection, $resource);
        $this->loadErrors = $loadErrors;
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(Task::class, TaskResource::class);
    }

    /**
     * @return AbstractCollection
     * @throws Exception
     */
    protected function _afterLoad()
    {
        $this->loadErrors();
        return parent::_afterLoad();
    }

    /**
     * @throws Exception
     */
    private function loadErrors() : void
    {
        $items = $this->getItems();
        if (empty($items)) {
            return;
        }

        $ids = array_keys($items);
        $errors = $this->loadErrors->execute($ids);
        foreach ($errors as $taskId => $error) {
            if (!isset($items[$taskId])) {
                continue;
            }

            $items[$taskId]->setError($error);
        }
    }
}
