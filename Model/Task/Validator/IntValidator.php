<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Validator;

use Magento\Framework\Validation\ValidationResult;
use SearchSpring\Feed\Model\Task\ValidatorInterface;

class IntValidator implements ValidatorInterface
{
    /**
     * @var CreateValidationResult
     */
    private $createValidationResult;
    /**
     * @var bool
     */
    private $fieldRequired;
    /**
     * @var array
     */
    private $fields;

    /**
     * IntValidator constructor.
     * @param CreateValidationResult $createValidationResult
     * @param array $fields
     * @param bool $fieldRequired
     */
    public function __construct(
        CreateValidationResult $createValidationResult,
        array $fields = [],
        bool $fieldRequired = false
    ) {
        $this->createValidationResult = $createValidationResult;
        $this->fieldRequired = $fieldRequired;
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
            }

            $value = $payload[$field];
            if (!is_numeric($value)) {
                $errors[] = (string)__('"%1" field value must be numeric', $field);
            }
        }

        return $this->createValidationResult->create($errors);
    }
}
