<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage\File;

use Exception;
use Magento\Framework\App\ObjectManager;
use SearchSpring\Feed\Model\Feed\Storage\FileInterface;

class FileFactory
{
    /**
     * @var array
     */
    private $fileClassPool;

    /**
     * FileFactory constructor.
     * @param array $fileClassPool
     */
    public function __construct(
        array $fileClassPool = []
    ) {
        $this->fileClassPool = $fileClassPool;
    }

    /**
     * @param string $format
     * @return FileInterface
     * @throws Exception
     */
    public function create(string $format) : FileInterface
    {
        $fileClass = $this->fileClassPool[$format] ?? null;
        if (!$fileClass) {
            throw new Exception('fileClass is null');
        }

        /** @var FileInterface $file */
        $file = ObjectManager::getInstance()->create($fileClass);
        return $file;
    }

    /**
     * @param string $format
     * @return bool
     */
    public function isSupportedFormat(string $format) : bool
    {
        return isset($this->fileClassPool[$format]);
    }
}
