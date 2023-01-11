<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider\Attribute;

use Exception;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class ValueProcessor
{
    private $cache = [];

    private $sourceAttributes = [];
    /**
     * @param Attribute $attribute
     * @param $value
     * @return bool|string|null
     * @throws LocalizedException
     * @throws Exception
     */
    public function getValue(Attribute $attribute, $value)
    {
        $key = null;
        $code = $attribute->getAttributeCode();
        if (!is_object($value) && !is_array($value) && $this->isSourceAttribute($attribute)) {
            $key = $code . '_' . $value;
            if (isset($this->cache[$key])) {
                return $this->cache[$key];
            }
        }

        $result = null;
        if ($this->isSourceAttribute($attribute)) {
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

        if ($key) {
            $this->cache[$key] = $result;
        }

        return $result;
    }

    /**
     *
     */
    public function reset() : void
    {
        $this->cache = [];
        $this->sourceAttributes = [];
    }

    /**
     * @param Attribute $attribute
     * @return bool
     */
    private function isSourceAttribute(Attribute $attribute) : bool
    {
        $code = $attribute->getAttributeCode();
        if (!array_key_exists($code, $this->sourceAttributes)) {
            $this->sourceAttributes[$code] = $attribute->usesSource();
        }

        return $this->sourceAttributes[$code];
    }
}
