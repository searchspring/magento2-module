<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Exception;

use SearchSpring\Feed\Api\MetadataInterface;
use Throwable;

class CouldNotFetchResultException extends GenericException
{
    const TASK_ERROR_CODE = 200;
    const TASK_NOT_COMPLETE_CODE = 201;

    /**
     * CouldNotFetchResultException constructor.
     * @param string $status
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $status = null,
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        if (!$code) {
            $code = $status === MetadataInterface::TASK_STATUS_ERROR
                ? self::TASK_ERROR_CODE
                : self::TASK_NOT_COMPLETE_CODE;
        }

        if (!$message) {
            $message = $status === MetadataInterface::TASK_STATUS_ERROR
                ? (string) __('Could not fetch task result because of task execution error, see task entity for details')
                : (string) __('Could not fetch task result because task in not completed yet');
        }

        parent::__construct($message, $code, $previous);
    }
}
