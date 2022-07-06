<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage\Formatter;

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
     * Csv constructor.
     * @param JsonSerializer $json
     */
    public function __construct(
        JsonSerializer $json
    ) {
        $this->json = $json;
    }

    /**
     * @param array $data
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function format(array $data, FeedSpecificationInterface $feedSpecification): array
    {
        $columns = $this->getColumns($data);
        $result[] = $columns;
        foreach ($data as $item) {
            $result[] = $this->formatRow($item, $columns, $feedSpecification);
        }

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
