<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Aws\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;
use Magento\Framework\HTTP\AsyncClient\GuzzleWrapDeferred;
use Magento\Framework\HTTP\AsyncClient\RequestFactory;
use Psr\Http\Message\StreamInterface;
use SearchSpring\Feed\Exception\ClientException;
use Throwable;

class Client implements ClientInterface
{
    /**
     * @var RequestFactory
     */
    private $requestFactory;
    /**
     * @var ResponseInterfaceFactory
     */
    private $responseFactory;
    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * Client constructor.
     * @param GuzzleClient $client
     * @param RequestFactory $requestFactory
     * @param ResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        GuzzleClient $client,
        RequestFactory $requestFactory,
        ResponseInterfaceFactory $responseFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->client = $client;
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

        try {
            $options = [];
            $options[RequestOptions::HEADERS] = $headers;
            if ($content !== null) {
                $options[RequestOptions::BODY] = $content;
            }

            $responseWrapper = new GuzzleWrapDeferred(
                $this->client->requestAsync(
                    $method,
                    $url,
                    $options
                )
            );
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
