<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Validator;

use Magento\Framework\Validation\ValidationResult;
use SearchSpring\Feed\Model\Task\ValidatorInterface;

class BoolValidator implements ValidatorInterface
{
    /**
     * @var bool
     */
    private $fieldRequired;
    /**
     * @var CreateValidationResult
     */
    private $createValidationResult;
    /**
     * @var array
     */
    private $fields;

    /**
     * BoolValidator constructor.
     * @param CreateValidationResult $createValidationResult
     * @param array $fields
     * @param bool $fieldRequired
     */
    public function __construct(
        CreateValidationResult $createValidationResult,
        array $fields = [],
        bool $fieldRequired = false
    ) {
        $this->fieldRequired = $fieldRequired;
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
            if (!isset($payload[$field]) && !$this->fieldRequired) {
                continue;
            } elseif (!isset($payload[$field]) && $this->fieldRequired) {
                $errors[] = (string) __('%1 field is required', $field);
                continue;
            }

            $value = $payload[$field] ?? null;
            if (!is_bool($value) && $value !== 0 && $value !== 1 && $value !== '0' && $value !== '1') {
                $errors[] = (string)__('"%1" field value must be "0" or "1"', $field);
            }
        }

        return $this->createValidationResult->create($errors);
    }
}
