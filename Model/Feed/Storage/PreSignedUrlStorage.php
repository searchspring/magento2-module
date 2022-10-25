<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage;

use Exception;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Aws\PreSignedUrl;
use SearchSpring\Feed\Model\Feed\StorageInterface;

class PreSignedUrlStorage implements StorageInterface
{
    /**
     * @var FormatterPool
     */
    private $formatterPool;
    /**
     * @var PreSignedUrl
     */
    private $preSignedUrl;

    /**
     * PreSignedUrlStorage constructor.
     * @param FormatterPool $formatterPool
     * @param PreSignedUrl $preSignedUrl
     */
    public function __construct(
        FormatterPool $formatterPool,
        PreSignedUrl $preSignedUrl
    ) {
        $this->formatterPool = $formatterPool;
        $this->preSignedUrl = $preSignedUrl;
    }

    /**
     * @param array $data
     * @param FeedSpecificationInterface $feedSpecification
     * @throws Exception
     */
    public function save(array $data, FeedSpecificationInterface $feedSpecification): void
    {
        $format = $feedSpecification->getFormat();
        if (!$format) {
            throw new Exception((string) __('format cannot be empty'));
        }

        if (!$this->isSupportedFormat($format)) {
            throw new Exception((string) __('%1 is not supported format', $format));
        }

        $formatter = $this->formatterPool->get($format);
        $formattedData = $formatter->format($data, $feedSpecification);
        $this->preSignedUrl->save($feedSpecification, $formattedData);
    }

    /**
     * @param string $format
     * @return bool
     */
    public function isSupportedFormat(string $format): bool
    {
        return !is_null($this->formatterPool->get($format));
    }
}
