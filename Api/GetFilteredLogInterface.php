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

namespace SearchSpring\Feed\Api;

use SearchSpring\Feed\Api\Data\ApplicationLogResponseInterface;

/**
 * Filtered, size-limited log access. Unlike GetApplicationLogInterface it never returns the whole file.
 */
interface GetFilteredLogInterface
{
    /**
     * Get filtered lines of var/log/searchspring_feed.log
     *
     * @param bool $compressOutput
     * @param int $lastLines Number of lines from the end; ignored when startLine or endLine is provided.
     * @param int $startLine 1-based start line of the filtered result; 0 means the first line.
     * @param int $endLine 1-based end line of the filtered result; 0 means the last line.
     * @param string $keyword Case-sensitive plain string; only matching lines are returned.
     * @param string $startDate Date/datetime (e.g. 2026-01-01 or 2026-01-01T10:00:00); lines on or after.
     * @param string $endDate Date/datetime; lines on or before. A plain date includes the whole day.
     * @return \SearchSpring\Feed\Api\Data\ApplicationLogResponseInterface
     * @throws \Magento\Framework\Exception\InputException when a date cannot be parsed
     */
    public function getExtensionLog(
        bool $compressOutput = false,
        int $lastLines = 100,
        int $startLine = 0,
        int $endLine = 0,
        string $keyword = '',
        string $startDate = '',
        string $endDate = ''
    ): ApplicationLogResponseInterface;

    /**
     * Get filtered lines of var/log/exception.log
     *
     * @param bool $compressOutput
     * @param int $lastLines Number of lines from the end; ignored when startLine or endLine is provided.
     * @param int $startLine 1-based start line of the filtered result; 0 means the first line.
     * @param int $endLine 1-based end line of the filtered result; 0 means the last line.
     * @param string $keyword Case-sensitive plain string; only matching lines are returned.
     * @param string $startDate Date/datetime (e.g. 2026-01-01 or 2026-01-01T10:00:00); lines on or after.
     * @param string $endDate Date/datetime; lines on or before. A plain date includes the whole day.
     * @return \SearchSpring\Feed\Api\Data\ApplicationLogResponseInterface
     * @throws \Magento\Framework\Exception\InputException when a date cannot be parsed
     */
    public function getExceptionLog(
        bool $compressOutput = false,
        int $lastLines = 100,
        int $startLine = 0,
        int $endLine = 0,
        string $keyword = '',
        string $startDate = '',
        string $endDate = ''
    ): ApplicationLogResponseInterface;

    /**
     * Get filtered lines of var/log/cron.log. When no keyword is given, only searchspring_task lines are returned.
     *
     * @param bool $compressOutput
     * @param int $lastLines Number of lines from the end; ignored when startLine or endLine is provided.
     * @param int $startLine 1-based start line of the filtered result; 0 means the first line.
     * @param int $endLine 1-based end line of the filtered result; 0 means the last line.
     * @param string $keyword Case-sensitive plain string; only matching lines are returned.
     * @param string $startDate Date/datetime (e.g. 2026-01-01 or 2026-01-01T10:00:00); lines on or after.
     * @param string $endDate Date/datetime; lines on or before. A plain date includes the whole day.
     * @return \SearchSpring\Feed\Api\Data\ApplicationLogResponseInterface
     * @throws \Magento\Framework\Exception\InputException when a date cannot be parsed
     */
    public function getCronLog(
        bool $compressOutput = false,
        int $lastLines = 100,
        int $startLine = 0,
        int $endLine = 0,
        string $keyword = '',
        string $startDate = '',
        string $endDate = ''
    ): ApplicationLogResponseInterface;
}
