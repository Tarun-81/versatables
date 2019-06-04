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

namespace Cart2Quote\Quotation\Model\Quote;

/**
 * Class ConfigProvider
 * @package Cart2Quote\Quotation\Model\Quote
 */
class ConfigProvider extends \Magento\Checkout\Model\DefaultConfigProvider
{
    /**
     * Replace the checkout session with the quote session.
     *
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrlManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\CartItemRepositoryInterface $quoteItemRepository
     * @param \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManager
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Address\Mapper $addressMapper
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param \Magento\Directory\Model\Country\Postcode\ConfigInterface $postCodesConfig
     * @param \Magento\Checkout\Model\Cart\ImageProvider $imageProvider
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Shipping\Model\Config $shippingMethodConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrlManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Api\CartItemRepositoryInterface $quoteItemRepository,
        \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManager,
        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Directory\Model\Country\Postcode\ConfigInterface $postCodesConfig,
        \Magento\Checkout\Model\Cart\ImageProvider $imageProvider,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Shipping\Model\Config $shippingMethodConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        parent::__construct(
            $checkoutHelper,
            $quoteSession,
            $customerRepository,
            $customerSession,
            $customerUrlManager,
            $httpContext,
            $quoteRepository,
            $quoteItemRepository,
            $shippingMethodManager,
            $configurationPool,
            $quoteIdMaskFactory,
            $localeFormat,
            $addressMapper,
            $addressConfig,
            $formKey,
            $imageHelper,
            $viewConfig,
            $postCodesConfig,
            $imageProvider,
            $directoryHelper,
            $cartTotalRepository,
            $scopeConfig,
            $shippingMethodConfig,
            $storeManager,
            $paymentMethodManagement,
            $urlBuilder
        );
    }

    /**
     * Retrieve checkout URL
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->urlBuilder->getUrl('quotation/quote/index');
    }

    /**
     * Retrieve checkout URL
     *
     * @return string
     */
    public function pageNotFoundUrl()
    {
        return $this->urlBuilder->getUrl('quotation/quote/index');
    }

    /**
     * Retrieve default success page URL
     *
     * @return string
     */
    public function getDefaultSuccessPageUrl()
    {
        return $this->urlBuilder->getUrl('quotation/quote/success/');
    }
}
