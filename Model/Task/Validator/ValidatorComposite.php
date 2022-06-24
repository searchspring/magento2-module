<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Task\Validator;

use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use SearchSpring\Feed\Model\Task\ValidatorInterface;

class ValidatorComposite implements ValidatorInterface
{
    /**
     * @var array
     */
    private $validators;
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * ValidatorComposite constructor.
     * @param ValidationResultFactory $validationResultFactory
     * @param array $validators
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory,
        array $validators = []
    ) {
        $this->validators = $validators;
        $this->validationResultFactory = $validationResultFactory;
    }

    /**
     * @param array $payload
     * @return ValidationResult
     */
    public function validate(array $payload): ValidationResult
    {
        $sortedValidators = $this->sort($this->validators);
        $errors = [];
        foreach ($sortedValidators as $sortedValidator) {
            if (isset($sortedValidator['objectInstance'])) {
                /** @var ValidatorInterface $instance */
                $instance = $sortedValidator['objectInstance'];
                $validationResult = $instance->validate($payload);
                if ($validationResult->isValid()) {
                    continue;
                }

                $errors = array_merge($errors, $validationResult->getErrors());
                $shouldBreak = $sortedValidator['breakOnFailure'] ?? true;
                if ($shouldBreak) {
                    break;
                }
            }
        }

        return $this->validationResultFactory->create(['errors' => $errors]);
    }


    /**
     * @param array $data
     * @return array
     */
    private function sort(array $data)
    {
        usort($data, function (array $a, array $b) {
            return $this->getSortOrder($a) <=> $this->getSortOrder($b);
        });

        return $data;
    }

    /**
     * @param array $variable
     * @return int
     */
    private function getSortOrder(array $variable)
    {
        return !empty($variable['sortOrder']) ? (int) $variable['sortOrder'] : 0;
    }
}
