<?php
use Magento\TestFramework\Helper\Bootstrap;
use SearchSpring\Feed\Model\Task\GenerateFeed\Executor;
use SearchSpring\Feed\Test\Integration\Model\GenerateFeedInvalidMock;

$objectManager = Bootstrap::getObjectManager();
$objectManager->configure([
   Executor::class => [
        'arguments' => [
            'generateFeed' => [
                'instance' => GenerateFeedInvalidMock::class
            ]
        ]
    ]
]);
