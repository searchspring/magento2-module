<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed;

class DataProviderPool
{
    /**
     * @var DataProviderInterface[]
     */
    private $dataProviders;

    /**
     * DataProviderPool constructor.
     * @param array $dataProviders
     */
    public function __construct(
        array $dataProviders = []
    ) {
        $this->dataProviders = $dataProviders;
    }

    /**
     * @param array $ignoredFields
     * @return array
     */
    public function get(array $ignoredFields = []) : array
    {
        $result = [];
        foreach ($this->dataProviders as $key => $dataProvider) {
            if (!in_array($key, $ignoredFields)) {
                $result[$key] = $dataProvider;
            }
        }

        return $result;
    }
}
