<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Validator;

use Magento\Framework\Validation\ValidationResult;
use SearchSpring\Feed\Model\Task\ValidatorInterface;

class ValidatorComposite implements ValidatorInterface
{

    /**
     * @param array $payload
     * @return ValidationResult
     */
    public function validate(array $payload): ValidationResult
    {
        // TODO: Implement validate() method.
    }
}
