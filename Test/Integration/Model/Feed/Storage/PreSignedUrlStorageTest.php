<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Feed\Storage;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Api\MetadataInterface;
use SearchSpring\Feed\Model\Feed\Specification\Feed;
use SearchSpring\Feed\Model\Feed\SpecificationBuilderInterface;
use SearchSpring\Feed\Model\Feed\Storage\PreSignedUrlStorage;

/**
 *
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PreSignedUrlStorageTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var PreSignedUrlStorage
     */
    private $preSignedUrlStorage;
    /**
     * @var SpecificationBuilderInterface
     */
    private $feedSpecificationBuilder;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->preSignedUrlStorage = $this->objectManager->get(PreSignedUrlStorage::class);
        $this->feedSpecificationBuilder = $this->objectManager->get(SpecificationBuilderInterface::class);
        parent::setUp();
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configure_aws_client_mock.php
     *
     * @return void
     * @throws \Exception
     */
    public function testSave() : void
    {
        $feed = $this->getFeedSpecification();
        $data = $this->getData();
        $this->preSignedUrlStorage->save($data, $feed);
        // check that we achieve this place and dont have any exceptions
        $this->assertEquals(1,1);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configure_aws_client_mock.php
     *
     * @return void
     * @throws \Exception
     */
    public function testSaveWithEmptyFormat() : void
    {
        /** @var Feed $feed */
        $feed = $this->getFeedSpecification();
        $feed->setData(FeedSpecificationInterface::FORMAT, null);
        $data = $this->getData();
        $this->expectExceptionObject(new Exception((string) __('format cannot be empty')));
        $this->preSignedUrlStorage->save($data, $feed);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configure_aws_client_mock.php
     *
     * @return void
     * @throws \Exception
     */
    public function testSaveWithInvalidFormat() : void
    {
        /** @var Feed $feed */
        $feed = $this->getFeedSpecification();
        $format = '___test___';
        $feed->setData(FeedSpecificationInterface::FORMAT, $format);
        $data = $this->getData();
        $this->expectExceptionObject(new Exception((string) __('%1 is not supported format', $format)));
        $this->preSignedUrlStorage->save($data, $feed);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture SearchSpring_Feed::Test/_files/configure_aws_client_mock.php
     *
     * @return void
     * @throws \Exception
     */
    public function setIsSupportedFormat() : void
    {
        $this->assertTrue($this->preSignedUrlStorage->isSupportedFormat(MetadataInterface::FORMAT_JSON));
        $this->assertFalse($this->preSignedUrlStorage->isSupportedFormat('___test____'));
    }

    /**
     * @return FeedSpecificationInterface
     */
    private function getFeedSpecification() : FeedSpecificationInterface
    {
        return $this->feedSpecificationBuilder->build(['preSignedUrl' => 'https://testurl.com']);
    }

    /**
     * @return array
     */
    private function getData() : array
    {
        return [
            [
                'test_key' => 'Test Value'
            ],
            [
                'test_key' => 'Different Test Value'
            ]
        ];
    }
}
