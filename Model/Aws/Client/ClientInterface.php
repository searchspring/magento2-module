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
     * @param string|null $content
     * @return ResponseInterface
     * @throws ClientException
     */
    public function execute(string $method, string $url, ?string $content = null, array $headers = []) : ResponseInterface;
}
