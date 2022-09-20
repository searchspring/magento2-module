<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage\Formatter;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\Storage\FormatterInterface;

class Json implements FormatterInterface
{
    /**
     * @var JsonSerializer
     */
    private $json;

    /**
     * Json constructor.
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
     * @return string
     */
    public function format(array $data, FeedSpecificationInterface $feedSpecification): string
    {
        return $this->json->serialize($data);
    }
}
