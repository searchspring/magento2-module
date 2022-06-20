<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task;

use Magento\Framework\Validation\ValidationResult;

interface ValidatorInterface
{
    /**
     * @param array $payload
     * @return ValidationResult
     */
    public function validate(array $payload) : ValidationResult;
}
