<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Storage\File;

use Magento\Framework\Stdlib\DateTime\DateTime;

class NameGenerator
{
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * FileNameGenerator constructor.
     * @param DateTime $dateTime
     */
    public function __construct(
        DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
    }

    /**
     * @param array $options
     * @return string
     */
    public function generate(array $options) : string
    {
        $name = 'searchspring_';
        foreach ($options as $value) {
            $name .= $value . '_';
        }

        $name .= str_replace(['-', ' ', ':'], '_', $this->dateTime->gmtDate());
        return $name;
    }
}
