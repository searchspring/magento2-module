<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Validator;

use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validator\Url;
use SearchSpring\Feed\Model\Task\ValidatorInterface;

class UrlValidator implements ValidatorInterface
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
     * @var Url
     */
    private $urlValidator;

    /**
     * IntValidator constructor.
     * @param CreateValidationResult $createValidationResult
     * @param Url $urlValidator
     * @param array $fields
     * @param bool $fieldRequired
     */
    public function __construct(
        CreateValidationResult $createValidationResult,
        Url $urlValidator,
        array $fields = [],
        bool $fieldRequired = false
    ) {
        $this->createValidationResult = $createValidationResult;
        $this->fieldRequired = $fieldRequired;
        $this->fields = $fields;
        $this->urlValidator = $urlValidator;
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
            if (!$this->urlValidator->isValid((string) $value, ['http', 'https'])) {
                $errors[] = (string)__('"%1" field value must be valid url address', $field);
            }
        }

        return $this->createValidationResult->create($errors);
    }
}
