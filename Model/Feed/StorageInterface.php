<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use Exception;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface StorageInterface
{
    /**
     * @param FeedSpecificationInterface $feedSpecification
     */
    public function initiate(FeedSpecificationInterface $feedSpecification) : void;

    /**
     * @param array $data
     */
    public function addData(array $data) : void;

    /**
     * @param bool $deleteFile
     */
    public function commit(bool $deleteFile = true) : void;

    /**
     *
     */
    public function rollback() : void;

    /**
     *
     */
    public function getAdditionalData() : array;

    /**
     * @param string $format
     * @return bool
     */
    public function isSupportedFormat(string $format) : bool;
}
