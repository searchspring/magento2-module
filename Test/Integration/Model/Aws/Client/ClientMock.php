<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Test\Integration\Model\Aws\Client;

use SearchSpring\Feed\Exception\ClientException;
use SearchSpring\Feed\Model\Aws\Client\ClientInterface;
use SearchSpring\Feed\Model\Aws\Client\ResponseInterface;
use SearchSpring\Feed\Model\Aws\Client\ResponseInterfaceFactory;

class ClientMock implements ClientInterface
{
    /**
     * @var ResponseInterfaceFactory
     */
    private $responseFactory;

    /**
     * ClientMock constructor.
     * @param ResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        ResponseInterfaceFactory $responseFactory
    ) {
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array|null $content
     * @param array $headers
     * @return ResponseInterface
     */
    public function execute(string $method, string $url, ?array $content = null, array $headers = []): ResponseInterface
    {
        return $this->responseFactory->create([
            'code' => 200,
            'headers' => [],
            'body' => ''
        ]);
    }
}
