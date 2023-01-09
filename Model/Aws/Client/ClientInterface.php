<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Aws\Client;

use SearchSpring\Feed\Exception\ClientException;

interface ClientInterface
{
    /**
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param array|null $content
     * @return ResponseInterface
     * @throws ClientException
     */
    public function execute(string $method, string $url, ?array $content = null, array $headers = []) : ResponseInterface;
}
