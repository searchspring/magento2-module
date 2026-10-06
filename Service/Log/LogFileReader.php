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

namespace SearchSpring\Feed\Service\Log;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File;
use Psr\Log\LoggerInterface;

/**
 * Reads a log file without loading the whole file in memory.
 * Supports tail (last N lines), line range, keyword and date range filters.
 */
class LogFileReader
{
    /**
     * Maximum bytes to read per line.
     */
    private const MAX_LINE_READ_BYTES = 1048576;

    /**
     * Chunk size used for reverse reads when retrieving only last lines.
     */
    private const TAIL_READ_CHUNK_BYTES = 16384;

    /**
     * Upper limit of returned lines, protects the response size.
     */
    public const MAX_LINES = 10000;

    /**
     * Matches both "[2026-01-01T10:00:00.000000+00:00]" (Monolog 2+) and "[2026-01-01 10:00:00]" (Monolog 1).
     */
    private const TIMESTAMP_PATTERN = '/^\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}[^\]]*)\]/';

    /**
     * @var File
     */
    private $fileDriver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param File $fileDriver
     * @param LoggerInterface $logger
     */
    public function __construct(
        File $fileDriver,
        LoggerInterface $logger
    ) {
        $this->fileDriver = $fileDriver;
        $this->logger = $logger;
    }

    /**
     * @param string $logFile absolute path
     * @param int $lastLines number of lines from the end; ignored when startLine or endLine is provided
     * @param int $startLine 1-based start line of the filtered result; 0 means first line
     * @param int $endLine 1-based end line of the filtered result; 0 means last line
     * @param string $keyword case-sensitive plain string
     * @param string $startDate any strtotime() compatible date, e.g. 2026-01-01 or 2026-01-01T10:00:00
     * @param string $endDate any strtotime() compatible date; a plain date includes the whole day
     * @return string[]
     */
    public function read(
        string $logFile,
        int $lastLines = 100,
        int $startLine = 0,
        int $endLine = 0,
        string $keyword = '',
        string $startDate = '',
        string $endDate = ''
    ): array {
        try {
            if (!$this->fileDriver->isExists($logFile)) {
                return [];
            }

            $lastLines = min(max(1, $lastLines), self::MAX_LINES);
            $hasDateFilter = $startDate !== '' || $endDate !== '';
            $hasLineRange = $startLine > 0 || $endLine > 0;
            if (!$hasDateFilter && !$hasLineRange && $keyword === '') {
                return $this->readLastLines($logFile, $lastLines);
            }

            return $this->readFiltered(
                $logFile,
                $lastLines,
                $startLine,
                $endLine,
                $keyword,
                $startDate,
                $endDate
            );
        } catch (FileSystemException $exception) {
            $this->logger->error('Error reading log file', [
                'method' => __METHOD__,
                'file' => $logFile,
                'message' => $exception->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * @param string[] $lines
     * @return string base64url encoded gzdeflate output
     */
    public function compress(array $lines): string
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $compressed = gzdeflate(implode("\n", $lines), 9);
        if ($compressed === false) {
            $this->logger->warning('Failed to compress log output', ['method' => __METHOD__]);
            return '';
        }

        return rtrim(strtr(base64_encode($compressed), '+/', '-_'), '=');
    }

    /**
     * @param string $logFile
     * @param int $lastLines
     * @param int $startLine
     * @param int $endLine
     * @param string $keyword
     * @param string $startDate
     * @param string $endDate
     * @return string[]
     * @throws FileSystemException
     */
    private function readFiltered(
        string $logFile,
        int $lastLines,
        int $startLine,
        int $endLine,
        string $keyword,
        string $startDate,
        string $endDate
    ): array {
        $hasDateFilter = $startDate !== '' || $endDate !== '';
        $hasLineRange = $startLine > 0 || $endLine > 0;
        $startTs = $startDate !== '' ? (strtotime($startDate) ?: 0) : 0;
        $endTs = PHP_INT_MAX;
        if ($endDate !== '') {
            $endTs = preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)
                ? (strtotime($endDate . ' 23:59:59') ?: PHP_INT_MAX)
                : (strtotime($endDate) ?: PHP_INT_MAX);
        }

        $matchedLines = [];
        $matchedLineNumber = 0;
        // lines without timestamp (e.g. stack traces) follow the date match of the previous entry
        $previousEntryInRange = false;
        $handle = $this->fileDriver->fileOpen($logFile, 'rb');
        try {
            while (($line = $this->readLine($handle)) !== null) {
                if ($hasDateFilter) {
                    if (preg_match(self::TIMESTAMP_PATTERN, $line, $matches)) {
                        $lineTs = strtotime($matches[1]);
                        $previousEntryInRange = $lineTs !== false && $lineTs >= $startTs && $lineTs <= $endTs;
                    }

                    if (!$previousEntryInRange) {
                        continue;
                    }
                }

                if ($keyword !== '' && strpos($line, $keyword) === false) {
                    continue;
                }

                $matchedLineNumber++;
                if ($hasLineRange) {
                    if ($startLine > 0 && $matchedLineNumber < $startLine) {
                        continue;
                    }
                    if ($endLine > 0 && $matchedLineNumber > $endLine) {
                        break;
                    }
                    $matchedLines[] = $line;
                    if (count($matchedLines) >= self::MAX_LINES) {
                        break;
                    }
                    continue;
                }

                $matchedLines[] = $line;
                if (count($matchedLines) > $lastLines) {
                    array_shift($matchedLines);
                }
            }
        } finally {
            $this->closeFile($handle);
        }

        return $matchedLines;
    }

    /**
     * Reads the file backwards in chunks until enough lines are collected.
     *
     * @param string $logFile
     * @param int $lastLines
     * @return string[]
     * @throws FileSystemException
     */
    private function readLastLines(string $logFile, int $lastLines): array
    {
        $handle = $this->fileDriver->fileOpen($logFile, 'rb');
        try {
            $this->fileDriver->fileSeek($handle, 0, SEEK_END);
            $position = $this->fileDriver->fileTell($handle);
            if (!is_int($position) || $position <= 0) {
                return [];
            }

            $buffer = '';
            $lineBreakCount = 0;
            while ($position > 0 && $lineBreakCount <= $lastLines) {
                $readSize = min(self::TAIL_READ_CHUNK_BYTES, $position);
                $position -= $readSize;
                $this->fileDriver->fileSeek($handle, $position, SEEK_SET);
                $chunk = $this->fileDriver->fileRead($handle, $readSize);
                if ($chunk === '') {
                    break;
                }

                $buffer = $chunk . $buffer;
                $lineBreakCount += substr_count($chunk, "\n");
            }
        } finally {
            $this->closeFile($handle);
        }

        $buffer = rtrim($buffer, "\r\n");
        if ($buffer === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\n|\r/', $buffer) ?: [];
        return array_slice($lines, -$lastLines);
    }

    /**
     * stream_get_line() returns false (driver throws) when the file ends with a line break,
     * because EOF is only flagged after that failed read.
     *
     * @param resource $handle
     * @return string|null null at the end of file
     * @throws FileSystemException
     */
    private function readLine($handle): ?string
    {
        if ($this->fileDriver->endOfFile($handle)) {
            return null;
        }

        try {
            $line = $this->fileDriver->fileReadLine($handle, self::MAX_LINE_READ_BYTES, "\n");
        } catch (FileSystemException $exception) {
            if ($this->fileDriver->endOfFile($handle)) {
                return null;
            }
            throw $exception;
        }

        return rtrim($line, "\r\n");
    }

    /**
     * @param resource $handle
     * @return void
     */
    private function closeFile($handle): void
    {
        try {
            $this->fileDriver->fileClose($handle);
        } catch (FileSystemException $exception) {
            $this->logger->error('Error closing log file', [
                'method' => __METHOD__,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
