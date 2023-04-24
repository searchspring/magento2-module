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

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Math\Random;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;

class Json extends FileAbstract
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * Json constructor.
     * @param Filesystem $filesystem
     * @param Random $random
     * @param JsonSerializer $jsonSerializer
     * @param string $fileExtension
     * @param string $subDirectory
     */
    public function __construct(
        Filesystem $filesystem,
        Random $random,
        JsonSerializer $jsonSerializer,
        string $fileExtension = 'json',
        string $subDirectory = 'searchspring'
    ) {
        parent::__construct($filesystem, $random, $fileExtension, $subDirectory);
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param string $fileName
     * @param FeedSpecificationInterface $feedSpecification
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function initialize(string $fileName, FeedSpecificationInterface $feedSpecification): void
    {
        $this->initializeFile($fileName);
    }

    /**
     * @param array $data
     * @throws FileSystemException
     * @throws \Exception
     */
    public function appendData(array $data): void
    {
        if (!$this->isInitialized()) {
            throw new \Exception('file is not initialized yet');
        }

        $this->checkFile();
        $this->openFile();
        $file = $this->getFile();
        $data = $this->jsonSerializer->serialize($data) . "\n";
        $file->write($data);
        $data = [];
    }
}
