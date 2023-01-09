<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

interface FileInterface
{
    /**
     * @param string $fileName
     * @param FeedSpecificationInterface $feedSpecification
     */
    public function initialize(string $fileName, FeedSpecificationInterface $feedSpecification) : void;

    /**
     * @param array $data
     */
    public function appendData(array $data) : void;

    /**
     *
     */
    public function commit() : void;

    /**
     *
     */
    public function rollback() : void;

    /**
     * @return string|null
     */
    public function getName() : ?string;

    /**
     * @return string|null
     */
    public function getAbsolutePath() : ?string;

    /**
     * @return array
     */
    public function getFileInfo() : array;
}
