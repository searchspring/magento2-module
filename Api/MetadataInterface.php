<?php
/**
 * Copyright (C) 2023 Searchspring <https://searchspring.com>
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

interface MetadataInterface
{
    const TASK_STATUS_PENDING = 'pending';
    const TASK_STATUS_PROCESSING = 'processing';
    const TASK_STATUS_SUCCESS = 'success';
    const TASK_STATUS_ERROR = 'error';

    const FEED_GENERATION_TASK_CODE = 'feed_generation';

    const FORMAT_CSV = 'csv';
    const FORMAT_JSON = 'json';
}
