<?php
declare(strict_types=1);

namespace SearchSpring\Feed\Api;
use Magento\Catalog\Api\Data\ProductInterface;

interface ProductRepositoryInterface
{
    /**
     * Return a filtered product.
     *
     * @param int $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItem(int $id);

}
