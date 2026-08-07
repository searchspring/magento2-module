<?php
declare(strict_types=1);

namespace SearchSpring\Feed\Logger;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/searchspring_feed.log';

    /**
     * @param DriverInterface $filesystem
     * @param string|null $filePath
     * @param string|null $fileName
     */
    public function __construct(
        DriverInterface $filesystem,
        ?string $filePath = null,
        ?string $fileName = null
    ) {
        parent::__construct($filesystem, $filePath, $fileName);
    }
}
