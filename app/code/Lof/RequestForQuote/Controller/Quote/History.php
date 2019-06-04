<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuote\Controller\Quote;

use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class History extends \Lof\RequestForQuote\Controller\AbstractIndex implements OrderInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\RequestForQuote\Helper\Data $rfqData,
        \Lof\RequestForQuote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Lof\RequestForQuote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerSession = $customerSession;
        $this->rfqData = $rfqData;
        $this->_quoteCollectionFactory = $quoteCollectionFactory;
        $this->mageQuoteFactory = $mageQuoteFactory;
        $this->quoteFactory = $quoteFactory;
        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->rfqData->getConfig('general/enable') || !($customerId = $this->_customerSession->getCustomerId())) {
            throw new NotFoundException(__('Page not found.'));
        }
        if($customerId = $this->_customerSession->getCustomerId()){
            $email = $this->_customerSession->getCustomer()->getEmail();
            if($email) {
                $customer_info = [];
                $customer_info['customer_id'] = $customerId;
                $customer_info['email'] = $email;
                $customer_info['customer_group_id'] = $this->_customerSession->getCustomer()->getGroupId();
                
                $model = $this->_objectManager->create('Lof\RequestForQuote\Model\Quote');
                $quotes = $this->_quoteCollectionFactory->create()->addFieldToSelect(
                    '*'
                )
                ->addFieldToFilter('customer_id', 0)
                ->addFieldToFilter('email', $email)
                ->setOrder('entity_id');
                if($quotes) {
                    foreach($quotes as $quote) {
                        $model->updateCustomerForQuote($quote->getId(),$quote->getQuoteId(), $customer_info);
                    }
                }
            }
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('My Quotes'));

        $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        return $resultPage;
    }
}
