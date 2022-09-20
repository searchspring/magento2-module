<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Aws\Client;

class Response implements ResponseInterface
{
    /**
     * @var int
     */
    private $code;
    /**
     * @var array
     */
    private $headers;
    /**
     * @var mixed|null
     */
    private $body;

    /**
     * Response constructor.
     * @param int $code
     * @param array $headers
     * @param mixed|null $body
     */
    public function __construct(
        int $code,
        array $headers = [],
        $body = null
    ) {
        $this->code = $code;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
