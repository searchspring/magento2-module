<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Exception;

use Throwable;

class ValidationException extends GenericException
{
    const CODE = 1000;

    /**
     * ValidationException constructor.
     * @param array $messages
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        $messages = [],
        $code = 0,
        Throwable $previous = null
    ) {
        $message = '';
        foreach ($messages as $error) {
            $message .= $error . PHP_EOL;
        }

        parent::__construct($message, $code, $previous);
    }
}
