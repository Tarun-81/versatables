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

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model;

/**
 * Class Address
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model
 */
class Address
{
    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Address constructor.
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quoteSession
    ) {
        $this->quoteSession = $quoteSession;
    }


    /**
     * Unset the unwanted shipping rates if the quotation shipping rate is selected
     *
     * @param $subject
     * @param $result
     * @return array
     */
    public function afterGetGroupedAllShippingRates($subject, $result)
    {
        $sessionConfigData = $this->getSessionQuoteConfigData($subject->getQuoteId());
        if ($this->hasFixedShipping($sessionConfigData) &&
            isset($result[\Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE])) {
            return [
                \Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE =>
                    $result[\Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE]
            ];
        }

        return $result;
    }

    /**
     * Get quote data from the session
     *
     * @param int $quoteId
     * @return array
     */
    protected function getSessionQuoteConfigData($quoteId)
    {
        $data = [];
        $configData = $this->quoteSession->getData(\Cart2Quote\Quotation\Model\Session::QUOTATION_STORE_CONFIG_DATA);
        if (isset($configData[$quoteId])) {
            $data = $configData[$quoteId];
        }

        return $data;
    }

    /**
     * Has fixed shipping price
     *
     * @param array $configData
     * @return bool
     */
    protected function hasFixedShipping($configData)
    {
        return isset($configData['fixed_shipping_price']);
    }
}
