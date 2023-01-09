<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Option;

class TitleToFieldNameConverter
{
    /**
     * @param string $title
     * @return string
     */
    public static function convert(string $title) : string
    {
        $option = strtolower(preg_replace(
            '/_+/',
        '_',
            preg_replace('/[^a-z0-9_]+/i', '_', trim($title)))
        );
        return 'option_' . $option;
    }
}
