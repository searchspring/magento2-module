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

namespace SearchSpring\Feed\Model\Feed\Storage\File;

use Exception;
use Magento\Framework\App\ObjectManager;
use SearchSpring\Feed\Model\Feed\Storage\FileInterface;

class FileFactory
{
    /**
     * @var array
     */
    private $fileClassPool;

    /**
     * FileFactory constructor.
     * @param array $fileClassPool
     */
    public function __construct(
        array $fileClassPool = []
    ) {
        $this->fileClassPool = $fileClassPool;
    }

    /**
     * @param string $format
     * @return FileInterface
     * @throws Exception
     */
    public function create(string $format) : FileInterface
    {
        $fileClass = $this->fileClassPool[$format] ?? null;
        if (!$fileClass) {
            throw new Exception('fileClass is null');
        }

        /** @var FileInterface $file */
        $file = ObjectManager::getInstance()->create($fileClass);
        return $file;
    }

    /**
     * @param string $format
     * @return bool
     */
    public function isSupportedFormat(string $format) : bool
    {
        return isset($this->fileClassPool[$format]);
    }
}
