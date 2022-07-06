<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

use SearchSpring\Feed\Api\Data\TaskResultInterface;

interface FetchTaskResultInterface
{
    /**
     * @param int $id
     * @return TaskResultInterface
     */
    public function execute(int $id) : TaskResultInterface;
}
