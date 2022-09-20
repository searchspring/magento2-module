<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage\Formatter;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Math\Random;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Storage\FormatterInterface;

class Csv implements FormatterInterface
{
    /**
     * @var JsonSerializer
     */
    private $json;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var Random
     */
    private $random;

    /**
     * Csv constructor.
     * @param JsonSerializer $json
     * @param Filesystem $filesystem
     * @param Random $random
     */
    public function __construct(
        JsonSerializer $json,
        Filesystem $filesystem,
        Random $random
    ) {
        $this->json = $json;
        $this->filesystem = $filesystem;
        $this->random = $random;
    }

    /**
     * @param array $data
     * @param FeedSpecificationInterface $feedSpecification
     * @return string
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function format(array $data, FeedSpecificationInterface $feedSpecification): string
    {
        $columns = $this->getColumns($data);
        $formattedData[] = $columns;
        foreach ($data as $item) {
            $formattedData[] = $this->formatRow($item, $columns, $feedSpecification);
        }

        $directory = $this->filesystem->getDirectoryWrite(DirectoryList::TMP);
        $filename = $this->random->getRandomString(32);
        $file = $directory->openFile($filename);
        foreach ($formattedData as $item) {
            $file->writeCsv($item);
        }

        $result = $file->readAll();
        $file->close();
        $directory->delete($filename);

        return $result;
    }

    /**
     * @param array $row
     * @param array $columns
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    private function formatRow(array $row, array $columns, FeedSpecificationInterface $feedSpecification) : array
    {
        $result = [];
        $multiValuedSeparator = $feedSpecification->getMultiValuedSeparator();
        foreach($columns as $field) {
            if(isset($row[$field])) {
                $value = $row[$field];
                if (is_array($value)) {
                    // If value is an array of arrays or objects then json encode value
                    if (is_array(current($value)) || is_object(current($value))) {
                        $result[] = $this->json->serialize($value);
                    } else {
                        $result[] = implode($multiValuedSeparator, array_unique($value));
                    }
                } else {
                    $result[] = $value;
                }
            } else {
                $result[] = '';
            }
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getColumns(array $data) : array
    {
        $result = [];
        foreach ($data as $item) {
            $result = array_merge($result, array_keys($item));
        }

        return array_unique($result);
    }
}
