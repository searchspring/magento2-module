<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Webapi;

use Magento\Framework\Webapi\Exception;
use SearchSpring\Feed\Exception\CouldNotFetchResultException;
use SearchSpring\Feed\Exception\GenericException;
use SearchSpring\Feed\Exception\NoSuchEntityException;
use SearchSpring\Feed\Exception\UniqueTaskException;
use SearchSpring\Feed\Exception\ValidationException;
use Throwable;

class DefaultExceptionConverter implements ExceptionConverterInterface
{
    const DEFAULT_HTTP_CODE = 500;

    const TYPE_VALIDATION = 'validation';
    const TYPE_UNIQUE = 'unique';
    const TYPE_GENERAL = 'general';
    const TYPE_NO_SUCH_ENTITY = 'no_such_entity';
    const TYPE_COULD_NOT_FETCH_RESULT = 'could_not_fetch_result';
    /**
     * @var array
     */
    private $httpCodesMap = [
        self::TYPE_VALIDATION => 400,
        self::TYPE_UNIQUE => 400,
        self::TYPE_GENERAL => 500,
        self::TYPE_NO_SUCH_ENTITY => 404,
        self::TYPE_COULD_NOT_FETCH_RESULT => 400
    ];
    /**
     * @var array
     */
    private $typeMap = [
        CouldNotFetchResultException::class => self::TYPE_COULD_NOT_FETCH_RESULT,
        ValidationException::class => self::TYPE_VALIDATION,
        UniqueTaskException::class => self::TYPE_UNIQUE,
        NoSuchEntityException::class => self::TYPE_NO_SUCH_ENTITY,
        GenericException::class => self::TYPE_GENERAL
    ];
    /**
     * @var int
     */
    private $defaultHttpCode;

    /**
     * DefaultExceptionConverter constructor.
     * @param int $defaultHttpCode
     * @param array $typeMap
     * @param array $httpCodesMap
     */
    public function __construct(
        int $defaultHttpCode = self::DEFAULT_HTTP_CODE,
        array $typeMap = [],
        array $httpCodesMap = []
    ) {
        $this->httpCodesMap = array_merge($this->httpCodesMap, $httpCodesMap);
        $this->typeMap = array_merge($this->typeMap, $typeMap);
        $this->defaultHttpCode = $defaultHttpCode;
    }

    /**
     * @param Throwable $exception
     * @return Exception
     */
    public function convert(Throwable $exception): Exception
    {
        $exceptionType = $this->resolveType($exception);
        $httpCode = $this->httpCodesMap[$exceptionType] ?? $this->defaultHttpCode;
        $code = $exception instanceof GenericException ? $exception->getCode() : GenericException::CODE;
        $newException = new Exception(
            __($exception->getMessage()),
            $code,
            $httpCode
        );

        return $newException;
    }

    /**
     * @param Throwable $exception
     * @return string
     */
    private function resolveType(Throwable $exception) : string
    {
        $classHierarchy = array_merge([get_class($exception)], class_parents($exception));
        $result = null;
        foreach ($classHierarchy as $value) {
            if (isset($this->typeMap[$value])) {
                $result = (string) $this->typeMap[$value];
                break;
            }
        }
        return $result ?? self::TYPE_GENERAL;
    }
}
