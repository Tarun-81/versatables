<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Block\Quote\QuoteCheckout;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * Attribute Mapper
     *
     * @var \Magento\Ui\Component\Form\AttributeMapper
     */
    protected $attributeMapper;
    /**
     * Attribute Merger
     *
     * @var \Magento\Checkout\Block\Checkout\AttributeMerger
     */
    protected $merger;
    /**
     * Attribute Metadata Data Provider
     *
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;
    /**
     * Options
     *
     * @var \Magento\Customer\Model\Options
     */
    private $options;

    /**
     * Address Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Address
     */
    private $helper;

    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * LayoutProcessor constructor.
     * @param \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param \Magento\Ui\Component\Form\AttributeMapper $attributeMapper
     * @param \Magento\Checkout\Block\Checkout\AttributeMerger $merger
     * @param \Magento\Customer\Model\Options $options
     * @param \Cart2Quote\Quotation\Helper\Address $helper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider,
        \Magento\Ui\Component\Form\AttributeMapper $attributeMapper,
        \Magento\Checkout\Block\Checkout\AttributeMerger $merger,
        \Magento\Customer\Model\Options $options,
        \Cart2Quote\Quotation\Helper\Address $helper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->options = $options;
        $this->merger = $merger;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if (!$this->customerSession->isLoggedIn()
            && isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['billing-address'], $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress'])) {
            unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['billing-address']);
            unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']);

            return $jsLayout;
        }

        $attributesToConvert = [
            'prefix' => [$this->options, 'getNamePrefixOptions'],
            'suffix' => [$this->options, 'getNameSuffixOptions'],
        ];

        $allowGuest = $this->helper->getAllowGuestConfig();
        $elements = $this->getAddressAttributes();
        $elements = $this->convertElementsToSelect($elements, $attributesToConvert);

        // The following code is a workaround for custom address attributes
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['billing-address']['children']
            ['billing-address-fieldset']['children'])) {
            $fields = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['billing-address']['children']
            ['billing-address-fieldset']['children'];
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['billing-address']['children']
            ['billing-address-fieldset']['children'] = $this->merger->merge(
                $elements,
                'checkoutProvider',
                'billingAddress',
                $fields
            );

            /** Process Config data */
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['billing-address']['children']['billing-address-fieldset']['children'] = $this->processFields(
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['billing-address']['children']
                ['billing-address-fieldset']['children'],
                $this->getBillingAddressConfig()
            );
        };

        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children'])) {

            /** Process Config data */
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children'] = $this->processFields(
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                ['shippingAddress']['children']['shipping-address-fieldset']['children'],
                $this->getShippingAddressConfig()
            );
        };

        if ($this->customerSession->isLoggedIn() || !$allowGuest) {
            $jsLayout = $this->removeFirstAndLastName($jsLayout);
        }

        return $jsLayout;
    }

    /**
     * Display full form
     *
     * @return bool
     */
    private function getAllowFullForm()
    {
        return $this->helper->getEnableForm();
    }

    /**
     * Get address attributes
     *
     * @return array
     */
    private function getAddressAttributes()
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );

        $elements = [];
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if ($attribute->getIsUserDefined()) {
                continue;
            }

            $elements[$code] = $this->attributeMapper->map($attribute);
            if (isset($elements[$code]['label'])) {
                $label = $elements[$code]['label'];
                $elements[$code]['label'] = __($label);
            }
        }

        return $elements;
    }

    /**
     * Convert elements(like prefix and suffix) from inputs to selects when necessary
     *
     * @param array $elements address attributes
     * @param array $attributesToConvert fields and their callbacks
     * @return array
     */
    private function convertElementsToSelect($elements, $attributesToConvert)
    {
        $codes = array_keys($attributesToConvert);
        foreach (array_keys($elements) as $code) {
            if (!in_array($code, $codes)) {
                continue;
            }
            $options = call_user_func($attributesToConvert[$code]);
            if (!is_array($options)) {
                continue;
            }
            $elements[$code]['dataType'] = 'select';
            $elements[$code]['formElement'] = 'select';

            foreach ($options as $key => $value) {
                $elements[$code]['options'][] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }
        }

        return $elements;
    }

    /**
     * Process the settings
     *
     * @param array $fields
     * @param \stdClass $config
     * @return array
     */
    private function processFields($fields, $config)
    {
        foreach ($config as $fieldData) {
            if (!$fieldData->enabled) {
                unset($fields[$fieldData->name]);
            } else {
                $fields[$fieldData->name] = $this->convertSettingToFieldMapping($fields[$fieldData->name], $fieldData);
            }
        }

        return $fields;
    }

    /**
     * Convert the settings JSON to a field mapping
     *
     * @param array $elementData
     * @param \stdClass $fieldData
     * @return array
     */
    private function convertSettingToFieldMapping($elementData, $fieldData)
    {
        $elementData['visible'] = $fieldData->enabled;
        $elementData['sortOrder'] = $fieldData->sortOrder;
        $elementData['required'] = $fieldData->required;

        if (isset($elementData['children'], $elementData['children'][0])) {
            $elementData['children'][0]['validation']['required-entry'] = $fieldData->required;
            
            if (isset($elementData['children'][0]['additionalClasses'])) {
                $elementData['children'][0]['additionalClasses'] = $elementData['children'][0]['additionalClasses'] . ' ' . $fieldData->additionalCss;
            } else {
                $elementData['children'][0]['additionalClasses'] = $fieldData->additionalCss;
            }

            if ($fieldData->required) {
                $elementData['children'][0]['additionalClasses'] = $elementData['children'][0]['additionalClasses'] . ' _required';
            }
        } else {
            $elementData['validation']['required-entry'] = $fieldData->required;

            if (isset($elementData['additionalClasses'])) {
                $elementData['additionalClasses'] = $elementData['additionalClasses'] . ' ' . $fieldData->additionalCss;
            } else {
                $elementData['additionalClasses'] = $fieldData->additionalCss;
            }

            if ($fieldData->required) {
                $elementData['additionalClasses'] = $elementData['additionalClasses'] . ' _required';
            }
        }

        return $elementData;
    }

    /**
     * Get the billing configuration
     *
     * @return array
     */
    private function getBillingAddressConfig()
    {
        if ($this->customerSession->isLoggedIn() || $this->helper->getBillingAddressConfig() != null) {
            return $this->helper->getDefaultAddressConfig();
        } else {
            return $this->helper->getBillingAddressConfig();
        }
    }

    /**
     * Get the shipping configuration
     *
     * @return array
     */
    private function getShippingAddressConfig()
    {
        if ($this->customerSession->isLoggedIn() || $this->helper->getShippingAddressConfig() != null) {
            return $this->helper->getDefaultAddressConfig();
        } else {
            return $this->helper->getShippingAddressConfig();
        }
    }

    /**
     * Remove first and last name from the quotation fields
     *
     * @param array $jsLayout
     * @return array
     */
    private function removeFirstAndLastName($jsLayout)
    {
        unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['quotation-fields']['children']['shipping-address-fieldset']['children']['firstname']);

        unset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['quotation-fields']['children']['shipping-address-fieldset']['children']['lastname']);

        return $jsLayout;
    }
}
