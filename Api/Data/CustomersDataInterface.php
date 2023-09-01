<?php

namespace SearchSpring\Feed\Api\Data;

interface CustomersDataInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $value
     * @return null
     */
    public function setId(string $value);

    /**
     * @param string $value
     * @return null
     */
    public function setEmail(string $value);
}