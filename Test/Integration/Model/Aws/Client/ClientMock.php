<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
