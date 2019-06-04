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

namespace Cart2Quote\Quotation\Controller\Quote;

use Magento\Framework\Exception\AuthorizationException;

/**
 * Class ChangeRequest
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class ChangeRequest extends \Cart2Quote\Quotation\Controller\Quote
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $_quotationSession;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * History constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Cart2Quote\Quotation\Model\QuotationCart $cart
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_quotationSession = $quotationSession;
        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quotationSession,
            $quoteFactory,
            $resultPageFactory
        );
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws AuthorizationException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('quote_id');
        $quote = $this->_objectManager->create('Cart2Quote\Quotation\Model\Quote');
        $quote = $quote->load($id);
        if ($quote->getCustomerId() !== $this->_customerSession->getCustomerId()) {
            throw new AuthorizationException(
                __('This customer is not the owner of this Quote')
            );
        }
        $quote->setIsActive(true);
        $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_OPEN)
            ->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_CHANGE_REQUEST)->save();
        $this->_quotationSession->replaceQuote($quote);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('quotation/quote/index');

        return $resultRedirect;
    }
}
