<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Plugin\Rest;

use Magento\Framework\Webapi\Exception;
use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Api\GetStoresInfoInterface;
use SearchSpring\Feed\Model\Webapi\ExceptionConverterInterface;
use Throwable;

class GetStoreInfoConvertException
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
     * @param GetStoresInfoInterface $subject
     * @param callable $proceed
     * @return string
     * @throws Exception
     */
    public function aroundGetAsHtml(GetStoresInfoInterface $subject, callable $proceed) : string
    {
        try {
            $result = $proceed();
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
            $newException = $this->exceptionConverter->convert($exception);
            throw $newException;
        }

        return $result;
    }
}
