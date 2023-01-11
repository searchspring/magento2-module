<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric\Output;

use Psr\Log\LoggerInterface;
use SearchSpring\Feed\Model\Metric\OutputInterface;

class LogOutput implements OutputInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LogOutput constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param string $data
     */
    public function print(string $data): void
    {
        $this->logger->debug($data);
    }
}
