<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Exception;

use Throwable;

class GenericException extends \Exception
{
    const CODE = 10000;

    /**
     * GenericException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        if (!$code) {
            $code = static::CODE;
        }

        parent::__construct($message, $code, $previous);
    }
}
