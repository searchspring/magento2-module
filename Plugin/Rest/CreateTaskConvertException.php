<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Plugin\Rest;

use Magento\Framework\Webapi\Exception;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\CreateTaskInterface;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Model\Webapi\ExceptionConverterInterface;
use Throwable;

class CreateTaskConvertException
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
     * CreateTaskConvertException constructor.
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
     * @param CreateTaskInterface $subject
     * @param callable $proceed
     * @param string $type
     * @param $payload
     * @return TaskInterface
     * @throws Exception
     */
    public function aroundExecute(CreateTaskInterface $subject, callable $proceed, string $type, $payload) : TaskInterface
    {
        try {
            $result = $proceed($type, $payload);
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
            $newException = $this->exceptionConverter->convert($exception);
            throw $newException;
        }

        return $result;
    }
}
