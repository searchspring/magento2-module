<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Exception;

use Throwable;

class FeedFileDeletedException extends CouldNotFetchResultException
{
    const CODE = 202;

    /**
     * FeedFileDeletedException constructor.
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
            $message = (string) __('Could not fetch task result because feed file was deleted');
        }

        if (!$code) {
            $code = self::CODE;
        }

        parent::__construct(null, $message, $code, $previous);
    }
}
