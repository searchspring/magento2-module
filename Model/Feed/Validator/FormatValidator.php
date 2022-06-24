<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Validator;

use Magento\Framework\Validation\ValidationResult;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Model\Task\Validator\CreateValidationResult;
use SearchSpring\Feed\Model\Task\ValidatorInterface;

class FormatValidator implements ValidatorInterface
{
    /**
     * @var CreateValidationResult
     */
    private $createValidationResult;
    /**
     * @var array
     */
    private $availableFormats;
    /**
     * @var string
     */
    private $field;
    /**
     * @var bool
     */
    private $fieldRequired;

    /**
     * FormatValidator constructor.
     * @param CreateValidationResult $createValidationResult
     * @param array $availableFormats
     * @param string $field
     * @param bool $fieldRequired
     */
    public function __construct(
        CreateValidationResult $createValidationResult,
        array $availableFormats = [MetadataInterface::FORMAT_CSV, MetadataInterface::FORMAT_JSON],
        string $field = 'format',
        bool $fieldRequired = false
    ) {
        $this->createValidationResult = $createValidationResult;
        $this->availableFormats = $availableFormats;
        $this->field = $field;
        $this->fieldRequired = $fieldRequired;
    }

    /**
     * @param array $payload
     * @return ValidationResult
     */
    public function validate(array $payload): ValidationResult
    {
        $errors = [];
        if (!isset($payload[$this->field]) && !$this->fieldRequired) {
            return $this->createValidationResult->create($errors);
        }

        $value = $payload[$this->field];
        if (!in_array($value, $this->availableFormats)) {
            $errors[] = (string) __(
                '"%1" field value must be %2, %3 is not supported',
                $this->field,
                implode(',', $this->availableFormats),
                $value
            );
        }

        return $this->createValidationResult->create($errors);
    }
}
