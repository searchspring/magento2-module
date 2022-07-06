<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage\Writer;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Storage\WriterInterface;

class Json implements WriterInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * Json constructor.
     * @param Filesystem $filesystem
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        Filesystem $filesystem,
        JsonSerializer $jsonSerializer
    ) {
        $this->filesystem = $filesystem;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param string $path
     * @param string $directory
     * @param array $data
     * @throws FileSystemException
     */
    public function write(string $path, string $directory, array $data): void
    {
        $directory = $this->filesystem->getDirectoryWrite($directory);
        $file = $directory->openFile($path);
        $jsonData = $this->jsonSerializer->serialize($data);
        $file->write($jsonData);
        $file->close();
    }
}
