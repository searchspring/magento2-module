<?php
/**
 * Helper to fetch version data.
 *
 * This file is part of SearchSpring/Feed.
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace SearchSpring\Feed\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;

class LogInfo extends AbstractHelper
{
    public function __construct()
    {
    }

    public function deleteExtensionLogFile() : bool
    {
        $objectManager = ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $logPath = $directory->getPath('log');
        $logFile = $logPath . '/searchspring_feed.log';

        if (file_exists($logFile)) {
            unlink($logFile);
        }

        return true;
    }

    public function getExtensionLogFile(bool $compressOutput = false) : string
    {
        $result = '';

        $objectManager = ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $logPath = $directory->getPath('log');
        $logFile = $logPath . '/searchspring_feed.log';

        if (file_exists($logFile)) {
            $result = file_get_contents($logFile);

            if (strlen($result) > 0 and $compressOutput){
                $result = rtrim(strtr(base64_encode(gzdeflate($result, 9)), '+/', '-_'), '=');
            }
        }

        return $result;
    }

    public function deleteExceptionLogFile() : bool
    {
        $objectManager = ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $logPath = $directory->getPath('log');
        $logFile = $logPath . '/exception.log';

        if (file_exists($logFile)) {
            unlink($logFile);
        }

        return true;
    }

    public function getExceptionLogFile(bool $compressOutput = false) : string
    {
        $result = '';

        $objectManager = ObjectManager::getInstance();
        $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
        $logPath = $directory->getPath('log');
        $logFile = $logPath . '/exception.log';

        if (file_exists($logFile)) {
            $result = file_get_contents($logFile);

            if (strlen($result) > 0 and $compressOutput){
                $result = rtrim(strtr(base64_encode(gzdeflate($result, 9)), '+/', '-_'), '=');
            }
        }

        return $result;
    }
}
