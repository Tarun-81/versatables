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

namespace Lof\RequestForQuote\Controller\Track;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NotFoundException;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context
     * @param \Magento\Framework\View\Result\PageFactory
     * @param \Magento\Framework\Registry
     * @param \Lof\RequestForQuote\Model\QuoteFactory
     * @param \Magento\Quote\Model\QuoteFactory
     * @param \Lof\RequestForQuote\Helper\Data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Lof\RequestForQuote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
        \Lof\RequestForQuote\Helper\Data $rfqData
        ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->_coreRegistry        = $registry;
        parent::__construct($context);
        $this->mageQuoteFactory = $mageQuoteFactory;
        $this->quoteFactory = $quoteFactory;
        $this->rfqData = $rfqData;
    }

    public function execute()
    {
        if (!$this->rfqData->getConfig('general/enable')) {
            throw new NotFoundException(__('Page not found.'));
        }
        $resultPage = $this->resultPageFactory->create();
        if($data = $this->getRequest()->getPostValue()){
            $quoteId = isset($data['qid'])?$data['qid']:'';
            $email = isset($data['email'])?$data['email']:'';
        } else {
            $quoteId = $this->getRequest()->getParam('qid');
            $email = $this->getRequest()->getParam('email');
            $email = str_replace("%40","@", $email);
        }

        if ($quoteId && $email) {
            $collection = $this->quoteFactory->create()->getCollection();
            $collection->addFieldToFilter("email", $email);
            $collection->addFieldToFilter("increment_id", $quoteId);
            $quote = false;
            if($collection->getSize()) {
                $quote = $collection->getFirstItem();
            }
            if($quote){
                if ($this->rfqData->isExpired($quote)) {
                    $quote->setStatus(\Lof\RequestForQuote\Model\Quote::STATE_EXPIRED);
                    $quote->save();
                }
                $this->_coreRegistry->register('is_tracking', 1);
                $this->_coreRegistry->register('current_rfq_quote', $quote);
                $mageQuote = $this->mageQuoteFactory->create()->load($quote->getQuoteId());
                $this->_coreRegistry->register('current_quote', $mageQuote);
                $resultPage->getConfig()->getTitle()->set(__('Quote # %1', $quote->getIncrementId()));

                $this->_eventManager->dispatch(
                                'lof_rfq_controller_track_result',
                                ['mage_quote' => $mageQuote, 'lof_quote' => $quote]
                            );
            } else {
                $this->_coreRegistry->register('not_found_quote', true);
                $resultPage->getConfig()->getTitle()->set(__('Track your quote information'));
            }
        } else {
            $resultPage->getConfig()->getTitle()->set(__('Track your quote information'));
        }
        return $resultPage;
    }
}