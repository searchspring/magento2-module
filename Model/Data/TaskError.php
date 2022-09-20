<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Data;

use Magento\Framework\Api\AbstractSimpleObject;
use SearchSpring\Feed\Api\Data\TaskErrorInterface;

class TaskError extends AbstractSimpleObject implements TaskErrorInterface
{
    /**
     * @return int|null
     */
    public function getCode(): ?int
    {
        return !is_null($this->_get(self::CODE))
            ? (int) $this->_get(self::CODE)
            : null;
    }

    /**
     * @param int $code
     * @return TaskErrorInterface
     */
    public function setCode(int $code): TaskErrorInterface
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * @param string $message
     * @return TaskErrorInterface
     */
    public function setMessage(string $message): TaskErrorInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }
}
