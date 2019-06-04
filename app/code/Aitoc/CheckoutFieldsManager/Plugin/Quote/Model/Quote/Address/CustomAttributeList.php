<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Plugin\Quote\Model\Quote\Address;

use Aitoc\CheckoutFieldsManager\Model\AttributeMetadataDataProvider;
use Magento\Quote\Model\Quote\Address\CustomAttributeList as QuoteCustomAttributeList;

/**
 * Plugin for @see QuoteCustomAttributeList
 * name="cfm_add_attributes_to_quote_address"
 */
class CustomAttributeList
{
    /**
     * @var array
     */
    private $attributeCodes = [];

    /**
     * @var AttributeMetadataDataProvider
     */
    private $attributeMetadata;

    /**
     * @param AttributeMetadataDataProvider $attributeMetadata
     */
    public function __construct(AttributeMetadataDataProvider $attributeMetadata)
    {
        $this->attributeMetadata = $attributeMetadata;
    }

    /**
     * Retrieve list of quote address custom attributes
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param QuoteCustomAttributeList $object parent
     * @param array $attributes the result of the previous plugins
     *
     * @return array
     */
    public function afterGetAttributes(QuoteCustomAttributeList $object, $attributes)
    {
        if (empty($this->attributeCodes)) {
            $attributeCollection = $this->attributeMetadata->loadAttributesCollection();
            foreach ($attributeCollection as $attribute) {
                $code = $attribute->getAttributeCode();
                $this->attributeCodes[$code] = $code;
            }
        }

        return array_merge($attributes, $this->attributeCodes);
    }
}
