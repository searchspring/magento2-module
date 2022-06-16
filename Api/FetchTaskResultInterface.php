<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

interface FetchTaskResultInterface
{
    /**
     * @param int $id
     * @return mixed
     */
    public function execute(int $id);
}
