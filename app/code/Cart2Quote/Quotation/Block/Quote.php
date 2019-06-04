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

namespace Cart2Quote\Quotation\Block;

/**
 * Class Quote
 * @package Cart2Quote\Quotation\Block
 */
class Quote extends \Cart2Quote\Quotation\Block\Quote\AbstractQuote
{
    /**
     * Address helper
     *
     * @var \Cart2Quote\Quotation\Helper\Address
     */
    protected $addressHelper;

    /**
     * Catalog Url Builder
     *
     * @var \Magento\Catalog\Model\ResourceModel\Url
     */
    protected $catalogUrlBuilder;

    /**
     * Http Context
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Cart Helper
     *
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;

    /**
     * Customer Url
     *
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * Visibility Enabled
     *
     * @var bool
     */
    protected $visibilityEnabled;

    /**
     * Flag for knowing if the full form is set
     *
     * @var bool
     */
    protected $fullFormSet = false;

    /**
     * Quote constructor.
     * @param \Cart2Quote\Quotation\Helper\Address $addressHelper
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Address $addressHelper,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $quotationSession, $quotationHelper, $data);
        $this->cartHelper = $cartHelper;
        $this->catalogUrlBuilder = $catalogUrlBuilder;
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        $this->customerUrl = $customerUrl;
        $this->quotationHelper = $quotationHelper;
        $this->addressHelper = $addressHelper;
    }

    /**
     * Checks if the quote has an error
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->getQuote()->getHasError();
    }

    /**
     * Get the continue shopping URL
     *
     * @return string
     */
    public function getContinueShoppingUrl()
    {
        $url = $this->getData('continue_shopping_url');
        if ($url === null) {
            $url = $this->_quotationSession->getContinueShoppingUrl(true);
            if (!$url) {
                $url = $this->_urlBuilder->getUrl();
            }
            $this->setData('continue_shopping_url', $url);
        }

        return $url;
    }

    /**
     * Get quote item count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return $this->getQuote()->getItemsCount();
    }

    /**
     * Checks if the customer is logged in
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * Get the login url
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }

    /**
     * Check if guest quote request is allowed
     *
     * @return bool
     */
    public function isGuestQuoteRequestAllowed()
    {
        return $this->quotationHelper->isAllowedGuestQuoteRequest($this->getQuote());
    }

    /**
     * Get the request for quote form
     *
     * @return string
     */
    public function getForm()
    {
        $form = $this->getChildHtml('checkout.root');
        $this->fullFormSet = (bool)$this->getEnableForm();

        return $form;
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

    /**
     * Prepare Quote Item Product URLs
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_customerSession->setAfterAuthUrl($this->getUrl('quotation/quote'));
        $this->prepareItemUrls();
    }

    /**
     * Prepare quote items URLs
     *
     * @return void
     */
    public function prepareItemUrls()
    {
        $products = [];
        /** @var $item \Magento\Quote\Model\Quote\Item */
        foreach ($this->getItems() as $item) {
            $product = $item->getProduct();
            $option = $item->getOptionByCode('product_type');
            if ($option) {
                $product = $option->getProduct();
            }

            if ($item->getStoreId() != $this->_storeManager->getStore()->getId() &&
                !$item->getRedirectUrl() &&
                !$product->isVisibleInSiteVisibility()
            ) {
                $products[$product->getId()] = $item->getStoreId();
            }
        }

        if ($products) {
            $products = $this->catalogUrlBuilder->getRewriteByProductStore($products);
            foreach ($this->getItems() as $item) {
                $product = $item->getProduct();
                $option = $item->getOptionByCode('product_type');
                if ($option) {
                    $product = $option->getProduct();
                }

                if (isset($products[$product->getId()])) {
                    $object = new \Magento\Framework\DataObject($products[$product->getId()]);
                    $item->getProduct()->setUrlDataObject($object);
                }
            }
        }
    }

    /**
     * Return customer quote items
     *
     * @return array
     */
    public function getItems()
    {
        if ($this->getCustomItems()) {
            return $this->getCustomItems();
        }

        return parent::getItems();
    }
}
