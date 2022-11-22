<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Plugin\Rest;

use Magento\Framework\Exception\NoSuchEntityException as OriginalNoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\Data\TaskInterface;
use SearchSpring\Feed\Api\TaskRepositoryInterface;
use SearchSpring\Feed\Exception\NoSuchEntityException;
use SearchSpring\Feed\Model\Webapi\ExceptionConverterInterface;
use Throwable;

class GetTaskConvertException
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
     * @param TaskRepositoryInterface $subject
     * @param callable $proceed
     * @param int $id
     * @return TaskInterface
     * @throws Exception
     */
    public function aroundGet(TaskRepositoryInterface $subject, callable $proceed, int $id) : TaskInterface
    {
        try {
            $result = $proceed($id);
        } catch (OriginalNoSuchEntityException $exception) {
            $this->logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
            $newException = new NoSuchEntityException($exception->getMessage(), NoSuchEntityException::CODE, $exception);
            $convertedException = $this->exceptionConverter->convert($newException);
            throw $convertedException;
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
            $newException = $this->exceptionConverter->convert($exception);
            throw $newException;
        }

        return $result;
    }
}
