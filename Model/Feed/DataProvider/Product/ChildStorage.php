<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Product;

use Magento\Catalog\Api\Data\ProductInterface;

class ChildStorage
{
    /**
     * @var array
     */
    private $products = [];
    /**
     * @param ProductInterface[] $items
     */
    public function set(array $items) : void
    {
        $this->products = $items;
    }

    /**
     * @param int $id
     * @return ProductInterface[]|null
     */
    public function getById(int $id) : ?array
    {
        return $this->products[$id] ?? null;
    }

    /**
     * @return array
     */
    public function get() : array
    {
        return $this->products;
    }

    /**
     *
     */
    public function reset() : void
    {
        $this->products = [];
    }
}
