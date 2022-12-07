<?php

namespace SearchSpring\Feed\Test\Unit\Model\Task\Validator;

use Magento\Framework\Validation\ValidationResult;
use SearchSpring\Feed\Model\Task\Validator\BoolValidator;
use SearchSpring\Feed\Model\Task\Validator\CreateValidationResult;

class BoolValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CreateValidationResult
     */
    private $createValidationResultMock;

    private array $fields = [
        'customerId',
        'includeOutOfStock',
    ];

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->createValidationResultMock = $this->createMock(CreateValidationResult::class);
        $this->boolValidator = new BoolValidator(
            $this->createValidationResultMock,
            $this->fields,
            true
        );
    }

    /**
     * @param array $payload
     * @param int $result
     * @param array $errors
     * @return void
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $payload, int $result, array $errors)
    {
        $resultValidationMock = $this->getMockBuilder(ValidationResult::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->createValidationResultMock->expects($this->once())
            ->method('create')
            ->willReturn($resultValidationMock);
        $resultValidationMock->expects($this->any())
            ->method('getErrors')
            ->withAnyParameters()
            ->willReturn($errors);

        $this->assertSame(
            $result,
            count($this->boolValidator->validate($payload)->getErrors())
        );
    }

    /**
     * @return array[]
     */
    public function validateDataProvider(): array
    {
        return [
            [
                [
                    'stores' => 'default',
                    'customerId' => 1,
                    'childFields' => [
                        'manufacturer',
                        'country_of_manufacturer'
                    ],
                    'includeOutOfStock' => 1
                ],
                0,
                []
            ],
            [
                [
                    'customerId' => 2,
                    'childFields' => [
                        'manufacturer',
                        'country_of_manufacturer'
                    ]
                ],
                1,
                [1]
            ]
        ];
    }
}
