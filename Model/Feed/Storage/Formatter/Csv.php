<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage\Formatter;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\FieldsProvider;
use SearchSpring\Feed\Model\Feed\Storage\FormatterInterface;

class Csv implements FormatterInterface
{
    /**
     * @var JsonSerializer
     */
    private $json;
    /**
     * @var FieldsProvider
     */
    private $fieldsProvider;

    /**
     * Csv constructor.
     * @param JsonSerializer $json
     * @param FieldsProvider $fieldsProvider
     */
    public function __construct(
        JsonSerializer $json,
        FieldsProvider $fieldsProvider
    ) {
        $this->json = $json;
        $this->fieldsProvider = $fieldsProvider;
    }

    /**
     * @param array $data
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws NoSuchEntityException
     */
    public function format(array $data, FeedSpecificationInterface $feedSpecification): array
    {
        $formattedData = [];
        foreach ($data as $item) {
            $formattedData[] = $this->formatRow($item, $feedSpecification);
        }

        $data = [];
        return $formattedData;
    }

    /**
     * @param array $row
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     * @throws NoSuchEntityException
     */
    private function formatRow(array $row, FeedSpecificationInterface $feedSpecification) : array
    {
        $columns = $this->fieldsProvider->getFields($feedSpecification);
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
}
