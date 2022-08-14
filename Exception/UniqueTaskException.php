<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Exception;

use Throwable;

class UniqueTaskException extends GenericException
{
    const CODE = 1;

    /**
     * UniqueTaskException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        if (!$message) {
            $message = (string) __('Task is not unique');
        }

        parent::__construct($message, $code, $previous);
    }
}
