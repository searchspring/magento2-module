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

use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;

$eavConfig = Bootstrap::getObjectManager()->get(Config::class);
$secondAttribute = $eavConfig->getAttribute('catalog_product', 'test_configurable_second');

$eavConfig->clear();

/** @var $installer CategorySetup */
$installer = Bootstrap::getObjectManager()->create(CategorySetup::class);

if (!$secondAttribute->getId()) {

    /** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
    $attribute = Bootstrap::getObjectManager()->create(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
    );

    /** @var AttributeRepositoryInterface $attributeRepository */
    $attributeRepository = Bootstrap::getObjectManager()->create(AttributeRepositoryInterface::class);

    $secondAttribute->setData(
        [
            'attribute_code' => 'test_configurable_second',
            'entity_type_id' => $installer->getEntityTypeId('catalog_product'),
            'is_global' => 1,
            'is_user_defined' => 1,
            'frontend_input' => 'select',
            'is_unique' => 0,
            'is_required' => 0,
            'is_searchable' => 0,
            'is_visible_in_advanced_search' => 0,
            'is_comparable' => 0,
            'is_filterable' => 0,
            'is_filterable_in_search' => 0,
            'is_used_for_promo_rules' => 0,
            'is_html_allowed_on_front' => 1,
            'is_visible_on_front' => 0,
            'used_in_product_listing' => 0,
            'used_for_sort_by' => 0,
            'frontend_label' => ['Test Configurable Second'],
            'backend_type' => 'int',
            'option' => [
                'value' => [
                    'second_option_0' => ['Second Option 1'],
                    'second_option_1' => ['Second Option 2'],
                    'second_option_2' => ['Second Option 3'],
                    'second_option_3' => ['Second Option 4']
                ],
                'order' => [
                    'second_option_0' => 1,
                    'second_option_1' => 2,
                    'second_option_2' => 3,
                    'second_option_3' => 4
                ],
            ],
        ]
    );

    $attributeRepository->save($secondAttribute);

    /* Assign attribute to attribute set */
    $installer->addAttributeToGroup('catalog_product', 'Default', 'General', $secondAttribute->getId());
}

$eavConfig->clear();
