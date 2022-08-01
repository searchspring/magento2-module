<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Attribute;

use Exception;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class ValueProcessor
{
    /**
     * @param Attribute $attribute
     * @param $value
     * @return bool|string|null
     * @throws LocalizedException
     * @throws Exception
     */
    public static function getValue(Attribute $attribute, $value)
    {
        $result = null;
        if ($attribute->usesSource()) {
            $result = $attribute->getSource()->getOptionText($value);
        } else {
            $result = $value;
        }

        if (is_object($result)) {
            if ($result instanceof Phrase) {
                $result = $result->getText();
            } else {
                throw new Exception("Unknown value object type " . get_class($result));
            }
        }

        return $result;
    }
}
