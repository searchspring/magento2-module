<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;
use SearchSpring\Feed\Api\AppConfigInterface;

class AppConfig implements AppConfigInterface
{
    const PREFIX = 'searchspring_feed';
    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;
    /**
     * @var Http
     */
    private $http;
    /**
     * @var array
     */
    private $defaults = ['debug' => false, 'product_delete_file' => true];
    /**
     * @var string
     */
    private $prefix;

    /**
     * AppConfig constructor.
     * @param DeploymentConfig $deploymentConfig
     * @param Http $http
     * @param string $prefix
     * @param array $defaults
     */
    public function __construct(
        DeploymentConfig $deploymentConfig,
        Http $http,
        string $prefix = self::PREFIX,
        array $defaults = []
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->http = $http;
        $this->defaults = array_merge($this->defaults, $defaults);
        $this->prefix = $prefix;
    }

    /**
     * @param string $code
     * @return mixed
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function getValue(string $code)
    {
        $varPath = $this->buildVarPath($code);
        $envPath = $this->buildEnvPath($code);
        $result = $this->http->getServer($varPath);
        if (is_null($result)) {
            $result = $this->deploymentConfig->get($envPath);
        }

        if (is_null($result)) {
            $result = $this->defaults[$code] ?? null;
        }

        return $result;
    }

    /**
     * @return bool
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function isDebug(): bool
    {
        $value = $this->getValue('debug');
        return !is_null($value) ? (bool) $value : false;
    }

    /**
     * @param string $code
     * @return string
     */
    private function buildVarPath(string $code) : string
    {
        $path = $this->prefix . '_' . $code;
        return strtoupper($path);
    }

    /**
     * @param string $code
     * @return string
     */
    private function buildEnvPath(string $code) : string
    {
        $path = $this->prefix . '_' . $code;
        return str_replace('_', '/', $path);
    }
}
