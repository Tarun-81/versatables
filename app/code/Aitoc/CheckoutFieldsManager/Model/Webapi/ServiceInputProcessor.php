<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Webapi;

use Magento\Framework\Api\AttributeValue;

class ServiceInputProcessor extends \Magento\Framework\Webapi\ServiceInputProcessor
{
    /**
     * @param array $customAttributesValueArray
     * @param string $dataObjectClassName
     *
     * @return AttributeValue[]
     * @throws \Magento\Framework\Exception\SerializationException
     */
    protected function convertCustomAttributeValue($customAttributesValueArray, $dataObjectClassName)
    {
        foreach ($customAttributesValueArray as $key => $customAttribute) {
            if (is_array($customAttribute) && !array_key_exists('value', $customAttribute)) {
                $customAttributesValueArray[$key] =
                    [
                        AttributeValue::ATTRIBUTE_CODE => $key,
                        AttributeValue::VALUE => $customAttribute
                    ];
            }
        }

        return parent::convertCustomAttributeValue($customAttributesValueArray, $dataObjectClassName);
    }
}
