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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Shipping\Method\Form;

/**
 * Class Quotation
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Shipping\Method\Form
 */
class Quotation extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Shipping\Method\Form
{

    /**
     * Get method title
     *
     * @return string
     */
    public function getMethodTitle()
    {
        if ($this->getRate()->getMethodTitle()) {
            $methodTitle = $this->getRate()->getMethodTitle();
        } else {
            $methodTitle = $this->getRate()->getMethodDescription();
        }

        return $this->escapeHtml($methodTitle);
    }

    /**
     * Get the shipping rate
     *
     * @return \Magento\Quote\Model\Quote\Address\Rate
     */
    public function getRate()
    {
        return $this->getData('rate');
    }

    /**
     * Get tax helper
     *
     * @return \Magento\Tax\Helper\Data
     */
    public function getTaxHelper()
    {
        return $this->getData('tax_helper');
    }

    /**
     * Set tax helper
     *
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @return $this
     */
    public function setTaxHelper(\Magento\Tax\Helper\Data $taxHelper)
    {
        $this->setData('tax_helper', $taxHelper);
        return $this;
    }

    /**
     * Set the shipping rate
     *
     * @param \Magento\Quote\Model\Quote\Address\Rate $rate
     * @return $this
     */
    public function setRate(\Magento\Quote\Model\Quote\Address\Rate $rate)
    {
        $this->setData('rate', $rate);
        return $this;
    }

    /**
     * Get the shipping code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData('code');
    }

    /**
     * Set the shipping code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->setData('code', $code);
        return $this;
    }

    /**
     * Get the shipping button property
     *
     * @return string
     */
    public function getRadioProperty()
    {
        return $this->getData('radio_property');
    }

    /**
     * Set the radio button property
     *
     * @param string $code
     * @return $this
     */
    public function setRadioProperty($code)
    {
        $this->setData('radio_property', $code);
        return $this;
    }
}
