<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

interface MetadataInterface
{
    const TASK_STATUS_PENDING = 'pending';
    const TASK_STATUS_PROCESSING = 'processing';
    const TASK_STATUS_SUCCESS = 'success';
    const TASK_STATUS_ERROR = 'error';

    const FEED_GENERATION_TASK_CODE = 'feed_generation';

    const FORMAT_CSV = 'csv';
    const FORMAT_JSON = 'json';
}
