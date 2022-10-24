<?php
use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Model\Aws\PreSignedUrl;
use SearchSpring\Feed\Test\Integration\Model\Aws\Client\ClientMock;

// configure new group type
$objectManager = Bootstrap::getObjectManager();
$objectManager->configure([
   PreSignedUrl::class => [
        'arguments' => [
            'client' => [
                'instance' => ClientMock::class
            ]
        ]
    ]
]);
