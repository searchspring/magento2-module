<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Api;

interface GetStoresInfoInterface
{
    /**
     * @return string
     */
    public function getAsHtml() : string;

    /**
     * @return array
     */
    public function getAsJson(): array;
}
