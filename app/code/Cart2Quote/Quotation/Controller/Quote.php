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

use Magento\Catalog\Controller\Product\View\ViewInterface;

/**
 * Quotation Quote controller
 */
abstract class Quote extends \Magento\Framework\App\Action\Action implements ViewInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $_quotationSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Cart2Quote\Quotation\Model\QuotationCart $cart
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->cart = $cart;
        $this->_quotationSession = $quotationSession;
        $this->_quoteFactory = $quoteFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Getter for _quotationSession
     * @return \Cart2Quote\Quotation\Model\Session
     */
    public function getQuotationSession()
    {
        return $this->_quotationSession;
    }

    /**
     * Set back redirect url to response
     * @param null|string $backUrl
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function _goBack($backUrl = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($backUrl || $backUrl = $this->getBackUrl($this->_redirect->getRefererUrl())) {
            $resultRedirect->setUrl($backUrl);
        }

        return $resultRedirect;
    }

    /**
     * Get resolved back url
     * @param null $defaultUrl
     * @return mixed|null|string
     */
    protected function getBackUrl($defaultUrl = null)
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl && $this->_isInternalUrl($returnUrl)) {
            $this->messageManager->getMessages()->clear();

            return $returnUrl;
        }

        //use magento cart settings
        $shouldRedirectToCart = $this->_scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($shouldRedirectToCart || $this->getRequest()->getParam('in_quote')) {
            if ($this->getRequest()->getActionName() == 'add' && !$this->getRequest()->getParam('in_quote')) {
                $this->_quotationSession->setContinueShoppingUrl($this->_redirect->getRefererUrl());
            }

            return $this->_url->getUrl('quotation/quote');
        }

        return $defaultUrl;
    }

    /**
     * Check if URL corresponds store
     * @param string $url
     * @return bool
     */
    protected function _isInternalUrl($url)
    {
        if (strpos($url, 'http') === false) {
            return false;
        }

        /**
         * Url must start from base secure or base unsecure url
         */
        /** @var $store \Magento\Store\Model\Store */
        $store = $this->_storeManager->getStore();
        $unsecure = strpos($url, $store->getBaseUrl()) === 0;
        $secure = strpos($url, $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true)) === 0;

        return $unsecure || $secure;
    }

    /**
     * Set success redirect url to response
     * @param null $successUrl
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function _successPage($successUrl = null)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($successUrl === null) {
            $successUrl = $this->_url->getUrl(
                'quotation/quote/success',
                [
                    'id' => $this->_quotationSession->getLastQuoteId()
                ]
            );
        }
        $resultRedirect->setUrl($successUrl);

        return $resultRedirect;
    }
}
