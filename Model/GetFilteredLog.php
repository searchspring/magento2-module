<?php
/**
 * Copyright (C) 2026 Searchspring <https://searchspring.com>
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

namespace SearchSpring\Feed\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use SearchSpring\Feed\Api\Data\ApplicationLogResponseInterface;
use SearchSpring\Feed\Api\Data\ApplicationLogResponseInterfaceFactory;
use SearchSpring\Feed\Api\GetFilteredLogInterface;
use SearchSpring\Feed\Service\Log\LogFileReader;

class GetFilteredLog implements GetFilteredLogInterface
{
    public const EXTENSION_LOG_FILE = 'searchspring_feed.log';
    public const EXCEPTION_LOG_FILE = 'exception.log';
    public const CRON_LOG_FILE = 'cron.log';
    public const CRON_DEFAULT_KEYWORD = 'searchspring_task';

    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var LogFileReader
     */
    private $logFileReader;
    /**
     * @var ApplicationLogResponseInterfaceFactory
     */
    private $responseFactory;

    /**
     * @param DirectoryList $directoryList
     * @param LogFileReader $logFileReader
     * @param ApplicationLogResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        DirectoryList $directoryList,
        LogFileReader $logFileReader,
        ApplicationLogResponseInterfaceFactory $responseFactory
    ) {
        $this->directoryList = $directoryList;
        $this->logFileReader = $logFileReader;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @inheritDoc
     */
    public function getExtensionLog(
        bool $compressOutput = false,
        int $lastLines = 100,
        int $startLine = 0,
        int $endLine = 0,
        string $keyword = '',
        string $startDate = '',
        string $endDate = ''
    ): ApplicationLogResponseInterface {
        return $this->read(
            self::EXTENSION_LOG_FILE,
            $compressOutput,
            $lastLines,
            $startLine,
            $endLine,
            $keyword,
            $startDate,
            $endDate
        );
    }

    /**
     * @inheritDoc
     */
    public function getExceptionLog(
        bool $compressOutput = false,
        int $lastLines = 100,
        int $startLine = 0,
        int $endLine = 0,
        string $keyword = '',
        string $startDate = '',
        string $endDate = ''
    ): ApplicationLogResponseInterface {
        return $this->read(
            self::EXCEPTION_LOG_FILE,
            $compressOutput,
            $lastLines,
            $startLine,
            $endLine,
            $keyword,
            $startDate,
            $endDate
        );
    }

    /**
     * @inheritDoc
     */
    public function getCronLog(
        bool $compressOutput = false,
        int $lastLines = 100,
        int $startLine = 0,
        int $endLine = 0,
        string $keyword = '',
        string $startDate = '',
        string $endDate = ''
    ): ApplicationLogResponseInterface {
        return $this->read(
            self::CRON_LOG_FILE,
            $compressOutput,
            $lastLines,
            $startLine,
            $endLine,
            $keyword !== '' ? $keyword : self::CRON_DEFAULT_KEYWORD,
            $startDate,
            $endDate
        );
    }

    /**
     * @param string $fileName
     * @param bool $compressOutput
     * @param int $lastLines
     * @param int $startLine
     * @param int $endLine
     * @param string $keyword
     * @param string $startDate
     * @param string $endDate
     * @return ApplicationLogResponseInterface
     * @throws FileSystemException
     * @throws \Magento\Framework\Exception\InputException
     */
    private function read(
        string $fileName,
        bool $compressOutput,
        int $lastLines,
        int $startLine,
        int $endLine,
        string $keyword,
        string $startDate,
        string $endDate
    ): ApplicationLogResponseInterface {
        $lines = $this->logFileReader->read(
            $this->directoryList->getPath(DirectoryList::LOG) . '/' . $fileName,
            $lastLines,
            $startLine,
            $endLine,
            $keyword,
            $startDate,
            $endDate
        );

        /** @var ApplicationLogResponseInterface $response */
        $response = $this->responseFactory->create();
        $response->setCompressed($compressOutput);
        if ($compressOutput) {
            return $response
                ->setLines([])
                ->setContent($lines ? $this->logFileReader->compress($lines) : null);
        }

        return $response->setLines($lines)->setContent(null);
    }
}
