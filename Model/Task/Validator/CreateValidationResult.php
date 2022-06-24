<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Validator;

use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;

class CreateValidationResult
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * CreateValidationResult constructor.
     * @param ValidationResultFactory $validationResultFactory
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory
    ) {
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * @param array $errors
     * @return ValidationResult
     */
    public function create(array $errors = []) : ValidationResult
    {
        return $this->validationResultFactory->create(['errors' => $errors]);
    }
}
