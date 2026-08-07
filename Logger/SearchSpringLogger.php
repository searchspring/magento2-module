<?php
declare(strict_types=1);

namespace SearchSpring\Feed\Logger;

use Monolog\Logger as MonologLogger;
use SearchSpring\Feed\Api\LoggerInterface;

class SearchSpringLogger extends MonologLogger implements LoggerInterface
{
}
