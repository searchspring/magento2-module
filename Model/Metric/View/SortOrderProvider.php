<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Metric\View;

use SearchSpring\Feed\Model\Metric\CollectorInterface;

class SortOrderProvider
{
    /**
     * @var array
     */
    private $orders = [
        CollectorInterface::CODE_PRODUCT_FEED => [
            'name' => 10,
            '__title__' => 20,
            'timer' => 30,
            'usage' => 40,
            'usage_diff' => 50,
            'usage_real' => 60,
            'usage_real_diff' => 70,
            'peak' => 80,
            'peak_diff' => 90,
            'peak_real' => 100,
            'peak_real_diff' => 110,
            'size' => 120,
            'size_diff' => 130,
            'size_readable' => 140,
            'size_diff_readable' => 150,
            'items_data_size' => 160,
            'items_data_count' => 170,
            'date' => 1000
        ]
    ];

    /**
     * SortOrderProvider constructor.
     * @param array $orders
     */
    public function __construct(
        array $orders = []
    ) {
        $this->orders = array_replace_recursive($this->orders, $orders);
    }

    /**
     * @param string $code
     * @return array
     */
    public function getSortOrder(string $code) : array
    {
        return $this->orders[$code] ?? [];
    }
}
