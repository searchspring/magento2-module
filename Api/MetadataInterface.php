<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

interface MetadataInterface
{
    const TASK_STATUS_PENDING = 'pending';
    const TASK_STATUS_PROCESSING = 'processing';
    const TASK_STATUS_SUCCESS = 'task_status_success';
    const TASK_STATUS_ERROR = 'error';
}
