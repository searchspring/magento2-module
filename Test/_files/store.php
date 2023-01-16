<?php

/** @var $store Store */

use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Magento\TestFramework\Helper\Bootstrap;

$store = Bootstrap::getObjectManager()->create(Store::class);
if (!$store->load('test', 'code')->getId()) {
    $store->setData(
        [
            'code' => 'test',
            'website_id' => '1',
            'group_id' => '1',
            'name' => 'Test Store',
            'sort_order' => '0',
            'is_active' => '1',
        ]
    );
    $store->save();
} else {
    if ($store->getId()) {
        /** @var Bootstrap $registry */
        $registry = Bootstrap::getObjectManager()->get(
            Registry::class
        );
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $store->delete();

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);

        $store = Bootstrap::getObjectManager()->create(Store::class);
        $store->setData(
            [
                'code' => 'test',
                'website_id' => '1',
                'group_id' => '1',
                'name' => 'Test Store',
                'sort_order' => '0',
                'is_active' => '1',
            ]
        );
        $store->save();
    }
}
