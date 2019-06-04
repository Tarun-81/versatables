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

use Cart2Quote\Quotation\Model\Config\Backend\Quote\QuotableCustomerGroups;

/**
 * Class Button
 */
class Button extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Cart2Quote\Quotation\Helper\QuotationCart
     */
    protected $quotationCartHelper;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var bool
     */
    protected $_visibilityEnabled;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Button constructor.
     * @param \Cart2Quote\Quotation\Helper\QuotationCart $quotationCartHelper
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\QuotationCart $quotationCartHelper,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data = []
    ) {
        $this->quotationCartHelper = $quotationCartHelper;
        $this->urlHelper = $urlHelper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $context->getRegistry();
        $this->_customerSession = $customerSession;
        $this->quotationHelper = $quotationHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check if has to show quote button on certain setting
     * @param string $setting - specifies the setting to check
     * @param boolean $quotable - require quotable check first
     * @return boolean
     */
    public function showQuoteButton($setting = 'cart2quote_quotation/global/show_btn_detail', $quotable = true)
    {
        $showButton = false;

        //check if visibility is enabled
        if (!$this->getIsQuotationEnabled()) {
            return $showButton;
        }

        if (!$quotable || ($quotable && $this->isQuotable())) {
            //Get config setting
            $configQuotable = $this->_scopeConfig->getValue($setting, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            //Evaluate config setting
            if ($configQuotable == 1) {
                $showButton = true;
            }
        }

        return $showButton;
    }

    /**
     * Check if Cart2Quote visibility is enabled
     * @return bool
     */
    public function getIsQuotationEnabled()
    {
        if (isset($this->_visibilityEnabled)) {
            return $this->_visibilityEnabled;
        }

        if ($this->quotationHelper->isFrontendEnabled()) {
            $this->_visibilityEnabled = true;

            return true;
        }

        $this->_visibilityEnabled = false;

        return false;
    }

    /**
     * Check if product is quotable
     * @return bool
     * @internal param \Magento\Catalog\Model\Product $product
     */
    public function isQuotable()
    {
        //Product setting - Product must be instanceof \Magento\Catalog\Model\Product
        $product = $this->getProduct();
        //Check if object is product
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $quotable = $product->getData('cart2quote_quotable');
            //Product configuration has priority over global config
            if ($quotable == "1") {
                return true;
            }
            if ($quotable == "0") {
                return false;
            }
        }

        //Get global config setting
        $configQuotable = $this->_scopeConfig->getValue(
            'cart2quote_quotation/global/quotable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($configQuotable == "0") {
            return false;
        }
        if ($configQuotable == "1") {
            return true;
        }
        if ($configQuotable == "2") {
            $configCustomerGroups = $this->_scopeConfig->getValue(
                QuotableCustomerGroups::QUOTABLE_CUSTOMER_GROUP,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $customerGroupId = $this->_customerSession->getCustomerGroupId();
            if (in_array($customerGroupId, explode(',', $configCustomerGroups))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve currently viewed product object
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        //get product
        if ($this->_coreRegistry->registry('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        } else {
            //get list product if product is not set
            if ($this->hasData('list_product')) {
                $this->setData('product', $this->getData('list_product'));
            }
        }

        return $this->getData('product');
    }

    /**
     * @return bool
     */
    public function showMessageNotLoggedIn()
    {
        $configQuotable = $this->_scopeConfig->getValue(
            'cart2quote_quotation/global/quotable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $configShowMessage = $this->_scopeConfig->getValue(
            'cart2quote_quotation/global/show_message_not_logged_in',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $configQuotable == "2" && !$this->_customerSession->isLoggedIn() && $configShowMessage;
    }

    /**
     * Whether redirect to cart enabled
     * @return bool
     */
    public function isRedirectToCartEnabled()
    {
        return $this->_scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get post parameters
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToQuotePostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToQuoteUrl($product);

        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * Retrieve url for add product to cart
     * Will return product view page URL if product has required options
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToQuoteUrl($product, $additional = [])
    {
        if ($product->getTypeInstance()->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = [];
            }
            $additional['_query']['options'] = 'cart';

            return $this->getProductUrl($product, $additional);
        }

        return $this->quotationCartHelper->getAddUrl($product, $additional);
    }

    /**
     * Retrieve Product URL using UrlDataObject
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional the route params
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }

            return $product->getUrlModel()->getUrl($product, $additional);
        }

        return '#';
    }

    /**
     * Check Product has URL
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        if ($product->hasUrlDataObject()) {
            if (in_array($product->hasUrlDataObject()->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                return true;
            }
        }

        return false;
    }
}
