<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface FormatterInterface
{
    /**
     * @param array $data
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function format(array $data, FeedSpecificationInterface $feedSpecification) : array;
}
