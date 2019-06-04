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

namespace Cart2Quote\Quotation\Model\Quote;

/**
 * Class QuotationConfigProvider
 * @package Cart2Quote\Quotation\Model\Quote
 */
class QuotationConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $session;

    /**
     * Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;

    /**
     * QuotationConfigProvider constructor.
     * @param \Cart2Quote\Quotation\Model\Session $session
     * @param \Cart2Quote\Quotation\Helper\Address $helper
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $session,
        \Cart2Quote\Quotation\Helper\Address $helper
    ) {
        $this->helper = $helper;
        $this->session = $session;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        $this->session->addConfigData([]);

        $this->prepareAddressConfig();

        return array_merge(
            $this->prepareDataField(\Cart2Quote\Quotation\Model\Session::QUOTATION_GUEST_FIELD_DATA),
            $this->prepareDataField(\Cart2Quote\Quotation\Model\Session::QUOTATION_FIELD_DATA),
            $this->prepareDataField(\Cart2Quote\Quotation\Model\Session::QUOTATION_PRODUCT_DATA),
            $this->prepareDataField(\Cart2Quote\Quotation\Model\Session::QUOTATION_STORE_CONFIG_DATA)
        );
    }

    /**
     * Add config fields regarding the shipping and billing configuration
     *
     * @return void
     */
    private function prepareAddressConfig()
    {
        $allowGuestConfig = $this->helper->getAllowGuestConfig();

        $config = [
            'allowGuest' => $allowGuestConfig,
            'allowForm' => $this->helper->getEnableForm()
        ];

        $this->session->setQuotationStoreConfigData($config);
    }

    /**
     * Prepare the session data field
     *
     * @param string $fieldName
     * @return array
     */
    private function prepareDataField($fieldName)
    {
        $output[$fieldName] = [];

        if (is_array($this->session->getData($fieldName))) {
            $output[$fieldName] = $this->session->getData($fieldName);
        }

        return $output;
    }
}