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

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Magento\Catalog\Setup\CategorySetup;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$installer = $objectManager->create(CategorySetup::class);
$attribute = $objectManager->create(AttributeFactory::class)->create();
$attributeRepository = $objectManager->create(ProductAttributeRepositoryInterface::class);
$entityType = $installer->getEntityTypeId(ProductAttributeInterface::ENTITY_TYPE_CODE);
if (!$attribute->loadByCode($entityType, 'visual_swatch_attribute')->getAttributeId()) {
    $attribute->setData(
        [
            'frontend_label' => ['Visual swatch attribute'],
            'entity_type_id' => $entityType,
            'frontend_input' => 'select',
            'backend_type' => 'int',
            'is_required' => '0',
            'attribute_code' => 'visual_swatch_attribute',
            'is_global' => '1',
            'is_user_defined' => 1,
            'is_unique' => '0',
            'is_searchable' => '0',
            'is_comparable' => '0',
            'is_filterable' => '1',
            'is_filterable_in_search' => '0',
            'is_used_for_promo_rules' => '0',
            'is_html_allowed_on_front' => '1',
            'used_in_product_listing' => '1',
            'used_for_sort_by' => '0',
            'swatch_input_type' => 'visual',
            'swatchvisual' => [
                'value' => [
                    'option_1' => '#555555',
                    'option_2' => '#aaaaaa',
                    'option_3' => '#ffffff',
                ],
            ],
            'optionvisual' => [
                'value' => [
                    'option_1' => ['option 1'],
                    'option_2' => ['option 2'],
                    'option_3' => ['option 3']
                ],
            ],
            'options' => [
                'option' => [
                    ['label' => 'Option 1', 'value' => 'option_1'],
                    ['label' => 'Option 2', 'value' => 'option_2'],
                    ['label' => 'Option 3', 'value' => 'option_3'],
                ],
            ],
        ]
    );
    $attributeRepository->save($attribute);
    $installer->addAttributeToGroup(
        ProductAttributeInterface::ENTITY_TYPE_CODE,
        'Default',
        'General',
        $attribute->getId()
    );
}
