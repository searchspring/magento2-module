<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Plugin\Rest;

use Magento\Framework\Webapi\Exception;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\Data\TaskResultInterface;
use SearchSpring\Feed\Api\FetchTaskResultInterface;
use SearchSpring\Feed\Model\Webapi\ExceptionConverterInterface;
use Throwable;

class FetchTaskResultConvertException
{
    /**
     * @var ExceptionConverterInterface
     */
    private $exceptionConverter;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FetchTaskResultConvertException constructor.
     * @param ExceptionConverterInterface $exceptionConverter
     * @param LoggerInterface $logger
     */
    public function __construct(
        ExceptionConverterInterface $exceptionConverter,
        LoggerInterface $logger
    ) {
        $this->exceptionConverter = $exceptionConverter;
        $this->logger = $logger;
    }

    /**
     * @param FetchTaskResultInterface $subject
     * @param callable $proceed
     * @param int $id
     * @return TaskResultInterface
     * @throws Exception
     */
    public function aroundExecute(FetchTaskResultInterface $subject, callable $proceed, int $id) : TaskResultInterface
    {
        try {
            $result = $proceed($id);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
            $newException = $this->exceptionConverter->convert($exception);
            throw $newException;
        }

        return $result;
    }
}
