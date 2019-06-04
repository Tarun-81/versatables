<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Plugin\Quote\Model\Quote;

use Aitoc\CheckoutFieldsManager\Model\ResourceModel\QuoteCustomerData\CollectionFactory as QuoteValueCollectionFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Quote\Model\Quote\Address as QuoteAddress;

/**
 * Plugin for @see QuoteAddress
 * name="cfm_add_attributes_to_quote_address"
 */
class Address
{
    /**
     * @var QuoteValueCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var AttributeValueFactory
     */
    private $customAttributeFactory;

    /**
     * @param QuoteValueCollectionFactory $collectionFactory
     * @param AttributeValueFactory $customAttributeFactory
     */
    public function __construct(
        QuoteValueCollectionFactory $collectionFactory,
        AttributeValueFactory $customAttributeFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->customAttributeFactory = $customAttributeFactory;
    }

    /**
     * Load CFM values for quote.
     * AddressType is used like checkout step (see aitoc_checkout_eav_attribute DB table)
     *
     * @param QuoteAddress $address
     * @param array|null $result
     *
     * @return array
     */
    public function afterGetCustomAttributes(QuoteAddress $address, $result)
    {
        if ($result || !$address->getQuoteId() || !$address->getAddressType()) {
            return $result;
        }

        $collection = $this->collectionFactory->create()
            ->prepareForCustomAttributesLoad($address->getQuoteId(), $address->getAddressType());
        $attributes = [];

        /** @var \Aitoc\CheckoutFieldsManager\Model\QuoteCustomerData $quoteAttributeValue */
        foreach ($collection as $quoteAttributeValue) {
            $code = $quoteAttributeValue->getData('attribute_code');
            if ($code) {
                $attributes[$code] = $this->customAttributeFactory->create()
                    ->setAttributeCode($code)
                    ->setValue($quoteAttributeValue->getValue());
            }
        }
        $address->setCustomAttributes($attributes);

        return $attributes;
    }
}
