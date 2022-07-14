<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class ContextManager implements ContextManagerInterface
{
    /**
     * @var ContextManagerInterface[]
     */
    private $processors;

    /**
     * ContextManager constructor.
     * @param array $processors
     */
    public function __construct(
        array $processors = []
    ) {
        $this->processors = $processors;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     */
    public function setContextFromSpecification(FeedSpecificationInterface $feedSpecification): void
    {
        foreach ($this->processors as $processor) {
            $processor->setContextFromSpecification($feedSpecification);
        }
    }

    /**
     *
     */
    public function resetContext(): void
    {
        foreach ($this->processors as $processor) {
            $processor->resetContext();
        }
    }
}
