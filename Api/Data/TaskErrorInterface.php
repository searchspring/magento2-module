<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api\Data;

interface TaskErrorInterface
{
    const CODE = 'code';
    const MESSAGE = 'message';
    /**
     * @return int|null
     */
    public function getCode() : ?int;

    /**
     * @param int $code
     * @return TaskErrorInterface
     */
    public function setCode(int $code) : self;

    /**
     * @return string|null
     */
    public function getMessage() : ?string;

    /**
     * @param string $message
     * @return TaskErrorInterface
     */
    public function setMessage(string $message) : self;
}
