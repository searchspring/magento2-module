<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Validator;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validation\ValidationResult;
use SearchSpring\Feed\Model\Task\ValidatorInterface;

class CustomerValidator implements ValidatorInterface
{
    /**
     * @var CreateValidationResult
     */
    private $createValidationResult;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var string
     */
    private $field;
    /**
     * @var bool
     */
    private $fieldRequired;

    /**
     * CustomerValidator constructor.
     * @param CreateValidationResult $createValidationResult
     * @param CustomerRepositoryInterface $customerRepository
     * @param string $field
     * @param bool $fieldRequired
     */
    public function __construct(
        CreateValidationResult $createValidationResult,
        CustomerRepositoryInterface $customerRepository,
        string $field = 'customerId',
        bool $fieldRequired = false
    ) {
        $this->createValidationResult = $createValidationResult;
        $this->customerRepository = $customerRepository;
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

        $customerId = $payload[$this->field];
        if (is_null($customerId) || is_bool($customerId)) {
            return $this->createValidationResult->create($errors);
        }

        try {
            $this->customerRepository->getById((int) $customerId);
        } catch (NoSuchEntityException | LocalizedException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $this->createValidationResult->create($errors);
    }
}
