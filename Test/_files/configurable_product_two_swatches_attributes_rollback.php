<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/** @var ObjectManagerInterface $objectManager */
$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
/** @var ProductAttributeRepositoryInterface $productAttributeRepository */
$productAttributeRepository = $objectManager->create(ProductAttributeRepositoryInterface::class);
$options = $productAttributeRepository->get('text_swatch_attribute')->getOptions();
$secondAttributeOptions = $productAttributeRepository->get('visual_swatch_attribute')->getOptions();
array_shift($options);
array_shift($secondAttributeOptions);
$productsArray = [];

foreach ($options as $option) {
    foreach ($secondAttributeOptions as $secondAttrOption) {
        $productsArray[] = strtolower(
            str_replace(' ', '_', 'simple ' . $option->getLabel() . '_' . $secondAttrOption->getLabel())
        );
    }
}

$productsArray[] = 'configurable';
foreach ($productsArray as $sku) {
    try {
        $productRepository->deleteById($sku);
    } catch (NoSuchEntityException $e) {
        //Product already removed
    }
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

require __DIR__ . '/product_text_swatch_attribute_rollback.php';
require __DIR__ . '/product_visual_swatch_attribute_rollback.php';
