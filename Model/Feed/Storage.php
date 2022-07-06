<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\DateTime;
use SearchSpring\Feed\Api\Data\FeedInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Storage\FormatterPool;
use SearchSpring\Feed\Model\Feed\Storage\WriterPool;

class Storage implements StorageInterface
{
    /**
     * @var FormatterPool
     */
    private $formatterPool;
    /**
     * @var WriterPool
     */
    private $writerPool;
    /**
     * @var DateTime
     */
    private $dateTime;
    /**
     * @var string
     */
    private $directory;
    /**
     * @var string
     */
    private $systemDirectory;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Storage constructor.
     * @param FormatterPool $formatterPool
     * @param WriterPool $writerPool
     * @param DateTime $dateTime
     * @param Filesystem $filesystem
     * @param string $directory
     * @param string $systemDirectory
     */
    public function __construct(
        FormatterPool $formatterPool,
        WriterPool $writerPool,
        DateTime $dateTime,
        Filesystem $filesystem,
        string $directory = 'searchspring',
        string $systemDirectory = DirectoryList::VAR_DIR
    ) {
        $this->formatterPool = $formatterPool;
        $this->writerPool = $writerPool;
        $this->dateTime = $dateTime;
        $this->directory = $directory;
        $this->systemDirectory = $systemDirectory;
        $this->filesystem = $filesystem;
    }

    /**
     * @param array $data
     * @param FeedInterface $feed
     * @param FeedSpecificationInterface $feedSpecification
     * @return FeedInterface
     * @throws Exception
     */
    public function save(array $data, FeedInterface $feed, FeedSpecificationInterface $feedSpecification): FeedInterface
    {
        $format = $feedSpecification->getFormat();
        if (!$format) {
            throw new Exception();
        }

        if (!$this->isSupportedFormat($format)) {
            throw new Exception();
        }

        $formatter = $this->formatterPool->get($format);
        $formattedData = $formatter->format($data, $feedSpecification);
        $path = $this->generateFilePath($feedSpecification);
        $writer = $this->writerPool->get($format);
        $writer->write($path, $this->systemDirectory, $formattedData);
        $feed->setDirectoryType($this->systemDirectory)
            ->setFilePath($path);

        return $feed;
    }

    /**
     * @param string $format
     * @return bool
     */
    public function isSupportedFormat(string $format): bool
    {
        return !is_null($this->formatterPool->get($format)) && !is_null($this->writerPool->get($format));
    }

    /**
     * @param FeedInterface $feed
     * @throws FileSystemException
     */
    public function archive(FeedInterface $feed): void
    {
        $directory = $this->filesystem->getDirectoryWrite($feed->getDirectoryType());
        $fileName = basename($feed->getFilePath());
        $newPath = $this->generateArchiveFilePath($fileName);
        $directory->renameFile($feed->getFilePath(), $newPath);
        $feed->setFilePath($newPath);
    }

    /**
     * @param FeedInterface $feed
     * @return string
     * @throws FileSystemException
     */
    public function getRawContent(FeedInterface $feed): string
    {
        $directory = $this->filesystem->getDirectoryRead($feed->getDirectoryType());
        return $directory->readFile($feed->getFilePath());
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @return string
     */
    private function generateFilePath(FeedSpecificationInterface $feedSpecification) : string
    {
        $format = $feedSpecification->getFormat();
        $store = $feedSpecification->getStoreCode();
        $dateTime = str_replace([' ', ':'], '-', $this->dateTime->gmtDate());

        return $this->directory . '/feed_' . $store . '_' . $dateTime . '.' . $format;
    }

    /**
     * @param string $filename
     * @return string
     */
    private function generateArchiveFilePath(string $filename) : string
    {
        return $this->directory . '/archive/' . $filename;
    }
}
