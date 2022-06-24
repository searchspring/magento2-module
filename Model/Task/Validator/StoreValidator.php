<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Validator;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Validation\ValidationResult;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreIsInactiveException;
use SearchSpring\Feed\Model\Task\ValidatorInterface;

class StoreValidator implements ValidatorInterface
{
    /**
     * @var CreateValidationResult
     */
    private $createValidationResult;
    /**
     * @var string
     */
    private $field;
    /**
     * @var bool
     */
    private $fieldRequired;
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * StoreValidator constructor.
     * @param CreateValidationResult $createValidationResult
     * @param StoreRepositoryInterface $storeRepository
     * @param string $field
     * @param bool $fieldRequired
     */
    public function __construct(
        CreateValidationResult $createValidationResult,
        StoreRepositoryInterface $storeRepository,
        string $field = 'store',
        bool $fieldRequired = false
    ) {
        $this->createValidationResult = $createValidationResult;
        $this->field = $field;
        $this->fieldRequired = $fieldRequired;
        $this->storeRepository = $storeRepository;
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
        try {
            $this->storeRepository->getActiveStoreByCode($value);
        } catch (NoSuchEntityException $exception) {
            $errors[] = (string) __('Store "%1" doesn\'t exist', $value);
        } catch (StoreIsInactiveException $exception) {
            $errors[] = (string) __('Store "%1" is not active', $value);
        }

        return $this->createValidationResult->create($errors);
    }
}
