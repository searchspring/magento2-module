<?php

namespace SearchSpring\Feed\Api\Data;

interface SalesDataInterface
{
    /**
     * @return string
     */
    public function getOrderId(): string;

    /**
     * @return string
     */
    public function getCustomerId(): string;

    /**
     * @return string
     */
    public function getProductId(): string;

    /**
     * @return string
     */
    public function getQuantity(): string;

    /**
     * @return string
     * @return null
     */
    public function getPrice(): string;

    /**
     * @return string
     * @return null
     */
    public function getCreatedAt(): string;

    /**
     * @param string $value
     * @return null
     */
    public function setOrderId(string $value);

    /**
     * @param string $value
     * @return null
     */
    public function setCustomerId(string $value);

    /**
     * @param string $value
     * @return null
     */
    public function setProductId(string $value);

    /**
     * @param string $value
     * @return null
     */
    public function setQuantity(string $value);

    /**
     * @param string $value
     * @return null
     */
    public function setPrice(string $value);

    /**
     * @param string $value
     * @return null
     */
    public function setCreatedAt(string $value);
}