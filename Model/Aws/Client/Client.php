<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Aws\Client;

use GuzzleHttp\Psr7\Utils;
use Magento\Framework\HTTP\AsyncClient\Request;
use Magento\Framework\HTTP\AsyncClient\RequestFactory;
use Magento\Framework\HTTP\AsyncClientInterface;
use Psr\Http\Message\StreamInterface;
use SearchSpring\Feed\Exception\ClientException;
use SearchSpring\Feed\Model\Aws\Client\ResponseInterfaceFactory;
use Throwable;

class Client implements ClientInterface
{
    /**
     * @var AsyncClientInterface
     */
    private $asyncClient;
    /**
     * @var RequestFactory
     */
    private $requestFactory;
    /**
     * @var ResponseInterfaceFactory
     */
    private $responseFactory;

    /**
     * Client constructor.
     * @param AsyncClientInterface $asyncClient
     * @param RequestFactory $requestFactory
     * @param ResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        AsyncClientInterface $asyncClient,
        RequestFactory $requestFactory,
        ResponseInterfaceFactory $responseFactory
    ) {
        $this->asyncClient = $asyncClient;
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array|null $content
     * @param array $headers
     * @return ResponseInterface
     * @throws ClientException
     */
    public function execute(string $method, string $url, ?array $content = null, array $headers = []) : ResponseInterface
    {
        if ($content) {
            $content = $this->prepareContent($content);
        }

        $request = $this->requestFactory->create([
            'url' => $url,
            'headers' => $headers,
            'method' => $method,
            'body' => $content
        ]);

        try {
            $responseWrapper = $this->asyncClient->request($request);
            $response = $responseWrapper->get();
            $convertedResponse = $this->responseFactory->create([
                'code' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $response->getBody()
            ]);
        } catch (Throwable $exception) {
            throw new ClientException($exception->getMessage(), 0, $exception);
        }

        return $convertedResponse;
    }

    /**
     * @param array $content
     * @return mixed|StreamInterface
     */
    private function prepareContent(array $content)
    {
        $type = $content['type'] ?? 'default';
        if ($type === 'stream' && isset($content['file'])) {
            $result = Utils::streamFor(Utils::tryFopen($content['file'], 'r+'));
        } else {
            $result = $content['content'] ?? array_shift($content);
        }

        return $result;
    }
}
