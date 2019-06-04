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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Adminhtml quotation quote view abstract block
 */
abstract class AbstractView extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Quote create
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $_quoteCreate;

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * AbstractView constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Cart2Quote\Quotation\Model\Quote $quoteCreate
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Cart2Quote\Quotation\Model\Quote $quoteCreate,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_quoteCreate = $quoteCreate;
        $this->_coreRegistry = $registry;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $data
        );
    }

    /**
     * Retrieve create quote model object
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getCreateQuoteModel()
    {
        return $this->_quoteCreate;
    }

    /**
     * Retrieve quote model object
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        $quoteId = $this->getRequest()->getParam('quote_id');
        if ($quoteId) {
            return $this->_quoteCreate->load($quoteId);
        } else {
            return $this->_coreRegistry->registry('current_quote');
        }
    }
}
