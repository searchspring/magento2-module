<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage\Writer;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Storage\WriterInterface;

class Csv implements WriterInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Csv constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $path
     * @param string $directory
     * @param array $data
     * @throws FileSystemException
     */
    public function write(
        string $path,
        string $directory,
        array $data
    ): void {
        $directory = $this->filesystem->getDirectoryWrite($directory);
        $file = $directory->openFile($path);
        foreach ($data as $item) {
            $file->writeCsv($item);
        }

        $file->close();
    }
}
