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
 * Quote history block
 */
class History extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Sales data
     * @var \Magento\Sales\Helper\Data
     */
    protected $_salesData = null;

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $adminHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_salesData = $salesData;
        parent::__construct($context, $data);
        $this->adminHelper = $adminHelper;
    }

    /**
     * Get stat uses
     * @return array
     */
    public function getStatuses()
    {
        $state = $this->getQuote()->getState();
        $statuses = $this->getQuote()->getConfig()->getStateStatuses($state);
        return $statuses;
    }

    /**
     * Retrieve quote model
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Check allow to send quote comment email
     * @return bool
     */
    public function canSendCommentEmail()
    {
        return $this->_salesData->canSendOrderCommentEmail($this->getQuote()->getStore()->getId());
    }

    /**
     * Check allow to add comment
     * @return bool
     */
    public function canAddComment()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::comment') && $this->getQuote()->canComment();
    }

    /**
     * Customer Notification Applicable check method
     * @param  \Cart2Quote\Quotation\Model\Quote\Status\History $history
     * @return bool
     */
    public function isCustomerNotificationNotApplicable(\Cart2Quote\Quotation\Model\Quote\Status\History $history)
    {
        return $history->isCustomerNotificationNotApplicable();
    }

    /**
     * Replace links in string
     * @param array|string $data
     * @param null|array $allowedTags
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->adminHelper->escapeHtmlWithLinks($data, $allowedTags);
    }

    /**
     * Preparing global layout
     * @return $this
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('quote_history_block').parentNode, '" . $this->getSubmitUrl() . "')";
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Submit Comment'), 'class' => 'action-save action-secondary', 'onclick' => $onclick]
        );
        $this->setChild('submit_button', $button);
        return parent::_prepareLayout();
    }

    /**
     * Submit URL getter
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('sales/*/addComment', ['quote_id' => $this->getQuote()->getId()]);
    }
}
