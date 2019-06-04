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

/**
 * Class Onepage
 * @package Cart2Quote\Quotation\Block\Quote\QuoteCheckout
 */
class Onepage extends \Magento\Checkout\Block\Onepage
{

    /**
     * Layout Processor
     *
     * @var \Cart2Quote\Quotation\Block\Quote\QuoteCheckout\LayoutProcessor
     */
    protected $layoutProcessor;

    /**
     * Address helper
     *
     * @var \Cart2Quote\Quotation\Helper\Address
     */
    protected $addressHelper;

    /**
     * Onepage constructor.
     * @param \Cart2Quote\Quotation\Block\Quote\QuoteCheckout\LayoutProcessor $layoutProcessor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Cart2Quote\Quotation\Model\Quote\CompositeConfigProvider $configProvider
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Block\Quote\QuoteCheckout\LayoutProcessor $layoutProcessor,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Cart2Quote\Quotation\Model\Quote\CompositeConfigProvider $configProvider,
        \Cart2Quote\Quotation\Helper\Address $addressHelper,
        array $layoutProcessors = [],
        array $data = []
    ) {
        $this->layoutProcessor = $layoutProcessor;
        $this->addressHelper = $addressHelper;
        parent::__construct($context, $formKey, $configProvider, $layoutProcessors, $data);
    }

    /**
     * Get the JS layout
     *
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $name => $processor) {
            if (in_array($name, $this->getAllowedLayoutProcessors())) {
                $this->jsLayout = $processor->process($this->jsLayout);
            }
        }

        $this->jsLayout = $this->layoutProcessor->process($this->jsLayout);
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * Get the allowed layout processors
     * Other layout processors are ignored.
     *
     * @return array
     */
    protected function getAllowedLayoutProcessors()
    {
        return [
            'addressFormAttributes',
            'directoryData'
        ];
    }

    /**
     * Get allowed to use form
     *
     * @return bool
     */
    public function getEnableForm()
    {
        return $this->addressHelper->getEnableForm();
    }
}

