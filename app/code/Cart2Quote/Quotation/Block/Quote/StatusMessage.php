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

namespace Cart2Quote\Quotation\Block\Quote;

/**
 * Class StatusMessage
 * @package Cart2Quote\Quotation\Block\Quote
 */
class StatusMessage extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Data Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * StatusMessage constructor.
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->messageManager = $messageManager;
        $this->quotationHelper = $quotationHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $status = $this->getQuote()->getStatus();

        $this->setQuoteMessage($status);
    }

    /**
     * Retrieve current quote model instance
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->coreRegistry->registry('current_quote');
    }

    /**
     * Set success message at quote status
     * @param $status
     */
    protected function setQuoteMessage($status)
    {
        $quoteMessage = null;
        switch ($status) {
            case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW:
                $quoteMessage = __('Your Quote Request has been received and will be processed shortly.');
                break;
            case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_AUTO_PROPOSAL_SENT:
            case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT:
                if ($this->isCheckoutDisabled()) {
                    $quoteMessage = __('Your Quote Request is available.');
                } else {
                    $quoteMessage = __('Your Quote Request is available. To order use the Accept and Checkout button.');
                }
                break;
        }

        if (isset($quoteMessage)) {
            $this->messageManager->addSuccessMessage($quoteMessage);
        }
    }

    /**
     * Check disabled checkout
     *
     * @return bool
     */
    private function isCheckoutDisabled()
    {
        return $this->quotationHelper->isCheckoutDisabled();
    }
}

