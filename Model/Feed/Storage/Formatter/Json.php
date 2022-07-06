<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage\Formatter;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Storage\FormatterInterface;

class Json implements FormatterInterface
{

    /**
     * @param array $data
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function format(array $data, FeedSpecificationInterface $feedSpecification): array
    {
        return $data;
    }
}
