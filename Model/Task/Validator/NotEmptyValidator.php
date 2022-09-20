<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Validator;

use Magento\Framework\Validation\ValidationResult;
use SearchSpring\Feed\Model\Task\ValidatorInterface;

class NotEmptyValidator implements ValidatorInterface
{
    /**
     * @var CreateValidationResult
     */
    private $createValidationResult;
    /**
     * @var array
     */
    private $fields;

    /**
     * IntValidator constructor.
     * @param CreateValidationResult $createValidationResult
     * @param array $fields
     */
    public function __construct(
        CreateValidationResult $createValidationResult,
        array $fields = []
    ) {
        $this->createValidationResult = $createValidationResult;
        $this->fields = $fields;
    }

    /**
     * @param array $payload
     * @return ValidationResult
     */
    public function validate(array $payload): ValidationResult
    {
        $errors = [];
        foreach ($this->fields as $field) {
            if (!array_key_exists($field, $payload)
                || !isset($payload[$field])
                || (is_string($payload[$field]) && $payload[$field] !== '0' && empty($payload[$field]))
            ) {
                $errors[] = (string)__('"%1" field value must be not empty', $field);
            }
        }

        return $this->createValidationResult->create($errors);
    }
}
