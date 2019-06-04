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

namespace Cart2Quote\Quotation\Controller;

/**
 * Copytoquote controller
 */
abstract class Copytoquote extends \Magento\Framework\App\Action\Action
{
    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Checkout Session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $_quotationSession;

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Form Key Validator
     *
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * Quotation Cart
     *
     * @var \Cart2Quote\Quotation\Model\QuotationCart
     */
    protected $cart;

    /**
     * Object Copy Service
     *
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $_objectCopyService;

    /**
     * Quote Factory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * Copytoquote constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Cart2Quote\Quotation\Model\QuotationCart $cart
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_quotationSession = $quotationSession;
        $this->_storeManager = $storeManager;
        $this->_objectCopyService = $objectCopyService;
        $this->cart = $cart;
        $this->_quoteFactory = $quoteFactory;
        parent::__construct($context);
    }

    /**
     * @param string $data
     * @return string
     */
    public function getUrl($data = '')
    {
        return $this->_url->getUrl($data);
    }

    /**
     * Clone quote model
     * @param $quoteId
     * @return bool|\Magento\Quote\Model\Quote|mixed
     */
    protected function _cloneQuote($quoteId)
    {
        if (!isset($quoteId) || empty($quoteId)) {
            return false;
        }

        $quote = $this->_objectManager->create('Magento\Quote\Model\Quote');
        $quote = $quote->load($quoteId);

        if (!$quote->getId()) {
            $this->messageManager->addError(__('This quote no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } else {
            //clone the quote
            $quoteClone = $this->_quoteFactory->create();
            $quote = $quoteClone->copy($quote);
            $quote->save();
        }

        return $quote;
    }
}
