<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Aws\Client;

interface ResponseInterface
{
    /**
     * @return mixed
     */
    public function getBody();

    /**
     * @return int
     */
    public function getCode() : int;

    /**
     * @return array
     */
    public function getHeaders() : array;
}
