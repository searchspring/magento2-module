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
use Magento\Framework\Exception\InputException;
use Magento\Framework\Filesystem\Driver\File;
use Psr\Log\LoggerInterface;

/**
 * Reads a log file with bounded memory, never the whole file.
 * Supports tail (last N lines), line range, keyword and date range filters.
 *
 * Size policy:
 * - at most MAX_LINES lines and MAX_OUTPUT_BYTES bytes are returned
 * - a returned line is cut to MAX_LINE_BYTES and gets a "[truncated, N bytes]" suffix
 * - filters are applied to the first MAX_LINE_READ_BYTES of a line, the rest of the line is skipped
 * - the tail read reads at most MAX_OUTPUT_BYTES from the end of the file, an entry cut by this limit is dropped
 */
class LogFileReader
{
    /**
     * Upper limit of returned lines.
     */
    public const MAX_LINES = 10000;

    /**
     * Upper limit of returned bytes, also the read budget of the tail read.
     */
    public const MAX_OUTPUT_BYTES = 8388608;

    /**
     * Returned lines are cut to this length.
     */
    public const MAX_LINE_BYTES = 65536;

    /**
     * Part of a line kept in memory and used for the keyword and date filters.
     */
    private const MAX_LINE_READ_BYTES = 1048576;

    /**
     * Chunk size of the tail read.
     */
    private const TAIL_READ_CHUNK_BYTES = 65536;

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
     * @throws InputException when a date cannot be parsed
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
        $startTs = $startDate !== '' ? $this->parseDate($startDate, 'startDate') : null;
        $endTs = null;
        if ($endDate !== '') {
            $endTs = $this->parseDate(
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate) ? $endDate . ' 23:59:59' : $endDate,
                'endDate'
            );
        }

        try {
            if (!$this->fileDriver->isExists($logFile)) {
                return [];
            }

            $lastLines = min(max(1, $lastLines), self::MAX_LINES);
            if ($startTs === null && $endTs === null && $startLine <= 0 && $endLine <= 0 && $keyword === '') {
                return $this->readLastLines($logFile, $lastLines);
            }

            return $this->readFiltered($logFile, $lastLines, $startLine, $endLine, $keyword, $startTs, $endTs);
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
     * @param string $date
     * @param string $fieldName
     * @return int
     * @throws InputException
     */
    private function parseDate(string $date, string $fieldName): int
    {
        $timestamp = strtotime($date);
        if ($timestamp === false) {
            throw new InputException(
                __('Invalid %1 "%2", use e.g. 2026-01-01 or 2026-01-01T10:00:00', $fieldName, $date)
            );
        }

        return $timestamp;
    }

    /**
     * @param string $logFile
     * @param int $lastLines
     * @param int $startLine
     * @param int $endLine
     * @param string $keyword
     * @param int|null $startTs
     * @param int|null $endTs
     * @return string[]
     * @throws FileSystemException
     */
    private function readFiltered(
        string $logFile,
        int $lastLines,
        int $startLine,
        int $endLine,
        string $keyword,
        ?int $startTs,
        ?int $endTs
    ): array {
        $hasDateFilter = $startTs !== null || $endTs !== null;
        $hasLineRange = $startLine > 0 || $endLine > 0;
        $matchedLines = [];
        // key of the oldest kept line, unset() does not reindex the array unlike array_shift()
        $oldestKey = 0;
        $matchedBytes = 0;
        $matchedLineNumber = 0;
        // lines without timestamp (e.g. stack traces) follow the date match of the previous entry
        $previousEntryInRange = false;
        $handle = $this->fileDriver->fileOpen($logFile, 'rb');
        try {
            while (($line = $this->readLine($handle)) !== null) {
                [$line, $length] = $line;
                if ($hasDateFilter) {
                    if (preg_match(self::TIMESTAMP_PATTERN, $line, $matches)) {
                        $lineTs = strtotime($matches[1]);
                        $previousEntryInRange = $lineTs !== false
                            && ($startTs === null || $lineTs >= $startTs)
                            && ($endTs === null || $lineTs <= $endTs);
                    }

                    if (!$previousEntryInRange) {
                        continue;
                    }
                }

                if ($keyword !== '' && strpos($line, $keyword) === false) {
                    continue;
                }

                $matchedLineNumber++;
                if ($hasLineRange && $startLine > 0 && $matchedLineNumber < $startLine) {
                    continue;
                }
                if ($hasLineRange && $endLine > 0 && $matchedLineNumber > $endLine) {
                    break;
                }

                $line = $this->truncate($line, $length);
                $matchedLines[] = $line;
                $matchedBytes += strlen($line);
                if ($hasLineRange) {
                    // range: keep the first lines within the limits
                    if (count($matchedLines) >= self::MAX_LINES || $matchedBytes >= self::MAX_OUTPUT_BYTES) {
                        break;
                    }
                    continue;
                }

                // tail: keep the last lines within the limits
                while (count($matchedLines) > $lastLines
                    || (count($matchedLines) > 1 && $matchedBytes > self::MAX_OUTPUT_BYTES)
                ) {
                    $matchedBytes -= strlen($matchedLines[$oldestKey]);
                    unset($matchedLines[$oldestKey++]);
                }
            }
        } finally {
            $this->closeFile($handle);
        }

        return array_values($matchedLines);
    }

    /**
     * Reads the file backwards in chunks until enough lines are found or the read budget is used.
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

            $chunks = [];
            $trimTrailingLineBreaks = true;
            $bytesRead = 0;
            $lineBreakCount = 0;
            // one line break more than lines is needed to know the first line is complete
            while ($position > 0 && $lineBreakCount <= $lastLines && $bytesRead < self::MAX_OUTPUT_BYTES) {
                $readSize = min(self::TAIL_READ_CHUNK_BYTES, $position, self::MAX_OUTPUT_BYTES - $bytesRead);
                $position -= $readSize;
                $this->fileDriver->fileSeek($handle, $position, SEEK_SET);
                $chunk = $this->fileDriver->fileRead($handle, $readSize);
                if ($chunk === '') {
                    break;
                }

                $bytesRead += strlen($chunk);
                if ($trimTrailingLineBreaks) {
                    // trailing line breaks of the file only end the last line
                    $chunk = rtrim($chunk, "\r\n");
                    $trimTrailingLineBreaks = $chunk === '';
                }
                $chunks[] = $chunk;
                $lineBreakCount += substr_count($chunk, "\n");
            }
        } finally {
            $this->closeFile($handle);
        }

        // peak memory is about twice the read budget: the chunks and the joined buffer
        $buffer = implode('', array_reverse($chunks));
        unset($chunks);
        if ($buffer === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\n|\r/', $buffer) ?: [];
        unset($buffer);
        if ($position > 0 && count($lines) <= $lastLines) {
            // the read budget stopped the read inside the first line, it is incomplete
            array_shift($lines);
            if (!$lines) {
                return [sprintf(
                    '[the last log entry is larger than %d bytes, use startLine/endLine or keyword to read it]',
                    self::MAX_OUTPUT_BYTES
                )];
            }
        }

        return array_map(function (string $line) {
            return $this->truncate($line, strlen($line));
        }, array_slice($lines, -$lastLines));
    }

    /**
     * @param string $line
     * @param int $length original length of the line
     * @return string
     */
    private function truncate(string $line, int $length): string
    {
        if ($length <= self::MAX_LINE_BYTES) {
            return $line;
        }

        // cut before the lead byte of a UTF-8 character that would be split, invalid UTF-8 breaks the JSON response
        $cut = min(self::MAX_LINE_BYTES, strlen($line));
        while ($cut > 0 && $cut < strlen($line) && (ord($line[$cut]) & 0xC0) === 0x80) {
            $cut--;
        }

        return substr($line, 0, $cut) . sprintf(' ... [truncated, %d bytes]', $length);
    }

    /**
     * Keeps the first MAX_LINE_READ_BYTES of a line, the rest of the line is read in pieces and dropped.
     * stream_get_line() returns false (driver throws) when the file ends with a line break,
     * because EOF is only flagged after that failed read.
     *
     * @param resource $handle
     * @return array|null [string $line, int $length], null at the end of file
     * @throws FileSystemException
     */
    private function readLine($handle): ?array
    {
        $line = null;
        $length = 0;
        do {
            if ($this->fileDriver->endOfFile($handle)) {
                break;
            }

            try {
                $piece = $this->fileDriver->fileReadLine($handle, self::MAX_LINE_READ_BYTES, "\n");
            } catch (FileSystemException $exception) {
                if ($this->fileDriver->endOfFile($handle)) {
                    break;
                }
                throw $exception;
            }

            $line = $line ?? $piece;
            $length += strlen($piece);
        // a piece of the maximum length means the line break was not reached yet
        } while (strlen($piece) === self::MAX_LINE_READ_BYTES);

        if ($line === null) {
            return null;
        }

        $line = rtrim($line, "\r\n");
        return [$line, max($length, strlen($line))];
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
