<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Webapi;

use Magento\Framework\Webapi\Exception;
use Throwable;

interface ExceptionConverterInterface
{
    /**
     * @param Throwable $exception
     * @return Exception
     */
    public function convert(Throwable $exception) : Exception;
}
