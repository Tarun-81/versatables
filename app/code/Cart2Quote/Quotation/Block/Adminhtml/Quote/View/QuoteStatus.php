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

/**
 * View quote status dropdown
 */
class QuoteStatus extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
{
    /**
     * Data Form object
     * @var \Magento\Framework\Data\Form
     */
    protected $_form;

    /**
     * Quote Status Collection
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection
     */
    protected $_statusCollection;

    /**
     * QuoteStatus constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Cart2Quote\Quotation\Model\Quote $quoteCreate
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Cart2Quote\Quotation\Model\Quote $quoteCreate,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        array $data
    ) {
        $this->_statusCollection = $statusCollection;
        parent::__construct(
            $context,
            $sessionQuote,
            $quoteCreate,
            $orderCreate,
            $priceCurrency,
            $registry,
            $data
        );
    }

    /**
     * Get header css class
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-comment';
    }

    /**
     * Get header text
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Quote Status');
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->escapeHtml($this->getQuote()->getStatusLabel());
    }

    /**
     * Get all statusses in ascending order
     * @return array
     */
    public function getAllStatussesAsArray()
    {
        return $this->_statusCollection->addOrder('sort', 'ASC')->toOptionArray();
    }
}
