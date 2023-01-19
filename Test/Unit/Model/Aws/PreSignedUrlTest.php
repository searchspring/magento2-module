<?php

namespace SearchSpring\Feed\Test\Unit\Model\Aws;

use PHPUnit\Framework\TestCase;
use SearchSpring\Feed\Api\AppConfigInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Aws\Client\ClientInterface;
use SearchSpring\Feed\Model\Aws\Client\ResponseInterface;
use SearchSpring\Feed\Model\Aws\PreSignedUrl;

class PreSignedUrlTest extends TestCase
{
    private $clientMock;

    private $appConfigMock;

    private $preSignedUrl;

    public function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->appConfigMock = $this->createMock(AppConfigInterface::class);
        $this->preSignedUrl = new PreSignedUrl(
            $this->clientMock,
            $this->appConfigMock,
            [],
            [],
            5,
            30
        );
    }

    public function testSave()
    {
        $responseInterfaceMock = $this->getMockForAbstractClass(ResponseInterface::class);
        $responseInterfaceMock->expects($this->once())
            ->method('getCode')
            ->willReturn(200);
        $feedSpecificationMock = $this->getFeedSpecificationMock();
        $this->clientMock->expects($this->once())
            ->method('execute')
            ->withAnyParameters()
            ->willReturn($responseInterfaceMock);

        $this->preSignedUrl->save($feedSpecificationMock, ['test']);
    }

    /**
     * @return void
     */
    public function testSaveExceptionCase()
    {
        $feedSpecificationMock = $this->getFeedSpecificationMock();
        $this->expectException(\Exception::class);
        $this->preSignedUrl->save($feedSpecificationMock, ['test']);
    }

    private function getFeedSpecificationMock()
    {
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getPreSignedUrl')
            ->willReturn('test');

        return $feedSpecificationMock;
    }
}
