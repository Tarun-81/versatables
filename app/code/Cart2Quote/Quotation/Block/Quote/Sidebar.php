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

use Magento\Store\Model\ScopeInterface;

/**
 * Quote sidebar block
 */
class Sidebar extends AbstractQuote
{
    /**
     * Xml pah to quoteadv sidebar display value
     */
    const XML_PATH_QUOTATION_SIDEBAR_DISPLAY = 'cart2quote_quotation/global/show_sidebar';

    /**
     * Xml pah to quoteadv sidebar extra css
     */
    const XML_PATH_QUOTATION_SIDEBAR_EXTRA_CSS = 'cart2quote_advanced/general/extra_miniquote_css';

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * Sidebar constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        if (isset($data['jsLayout'])) {
            $this->jsLayout = array_merge_recursive($jsLayoutDataProvider->getData(), $data['jsLayout']);
            unset($data['jsLayout']);
        } else {
            $this->jsLayout = $jsLayoutDataProvider->getData();
        }
        parent::__construct($context, $customerSession, $quotationSession, $quotationHelper, $data);
        $this->_isScopePrivate = false;
        $this->imageHelper = $imageHelper;
    }

    /**
     * Returns miniquote config
     * @return array
     */
    public function getConfig()
    {
        return [
            'quoteCartUrl' => $this->getQuoteCartUrl(),
            'checkoutUrl' => $this->getQuoteCartUrl(),
            'updateItemQtyUrl' => $this->getUpdateItemQtyUrl(),
            'removeItemUrl' => $this->getRemoveItemUrl(),
            'imageTemplate' => $this->getImageHtmlTemplate(),
            'baseUrl' => $this->getBaseUrl(),
        ];
    }

    /**
     * Get quotation quote page url
     * @return string
     */
    public function getQuoteCartUrl()
    {
        return $this->getUrl('quotation/quote');
    }

    /**
     * Get update quote item url
     * @return string
     */
    public function getUpdateItemQtyUrl()
    {
        return $this->getUrl('checkout/sidebar/updateItemQty');
    }

    /**
     * Get remove quote item url
     * @return string
     */
    public function getRemoveItemUrl()
    {
        return $this->getUrl('checkout/sidebar/removeItem');
    }

    /**
     * @return string
     */
    public function getImageHtmlTemplate()
    {
        return $this->imageHelper->getFrame()
            ? 'Magento_Catalog/product/image'
            : 'Magento_Catalog/product/image_with_borders';
    }

    /**
     * Return base url.
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get one page checkout page url
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout');
    }

    /**
     * Define if Mini Shopping quote Pop-Up Menu enabled
     * @return bool
     */
    public function getIsNeedToDisplaySideBar()
    {
        $setting = $this->_scopeConfig->getValue(
            self::XML_PATH_QUOTATION_SIDEBAR_DISPLAY,
            ScopeInterface::SCOPE_STORE
        );
        return (bool)$setting;
    }

    /**
     * Return totals from custom quote if needed
     * @return array
     */
    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $quote = $this->getCustomQuote() ? $this->getCustomQuote() : $this->getQuote();
            $this->_totals = $quote->getTotals();
        }

        return $this->_totals;
    }

    /**
     * Retrieve subtotal block html
     * @return string
     */
    public function getTotalsHtml()
    {
        return $this->getLayout()->getBlock('quotation.quote.miniquote.totals')->toHtml();
    }

    /**
     * Return the extra CSS set in the backend
     */
    public function getExtraCSS()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_QUOTATION_SIDEBAR_EXTRA_CSS,
            ScopeInterface::SCOPE_STORE
        );
    }
}
