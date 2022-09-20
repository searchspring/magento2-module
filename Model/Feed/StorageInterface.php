<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use Exception;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface StorageInterface
{
    /**
     * @param array $data
     * @param FeedSpecificationInterface $feedSpecification
     * @throws Exception
     */
    public function save(array $data, FeedSpecificationInterface $feedSpecification) : void;

    /**
     * @param string $format
     * @return bool
     */
    public function isSupportedFormat(string $format) : bool;
}
