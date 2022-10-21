<?php
use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Model\Task\GenerateFeed\Executor;
use SearchSpring\Feed\Test\Integration\Model\GenerateFeedMock;

// configure new group type
$objectManager = Bootstrap::getObjectManager();
$objectManager->configure([
   Executor::class => [
        'arguments' => [
            'generateFeed' => [
                'instance' => GenerateFeedMock::class
            ]
        ]
    ]
]);
