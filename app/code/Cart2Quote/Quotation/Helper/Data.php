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

namespace Cart2Quote\Quotation\Helper;

/**
 * Class Data
 * @package Cart2Quote\Quotation\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @const auto user login
     */
    const AUTO_LOGIN_ENABLE = 'cart2quote_advanced/checkout/auto_user_login';

    /**
     * @const disable checkout
     */
    const CHECKOUT_DISABLE = 'cart2quote_advanced/checkout/accept_quote_without_checkout';

    /**
     * @const auto confirm proposal
     */
    const AUTO_CONFIRM_PROPOSAL = 'cart2quote_advanced/checkout/auto_confirm_proposal';

    /**
     * @const disable product remark
     */
    const DISABLE_PRODUCT_REMARK = 'cart2quote_advanced/remarks/disable_product_remark';

    /**
     * @const hide order references
     */
    const HIDE_ORDER_REFERENCES = 'cart2quote_quotation/global/hide_order_references';

    /**
     * Path to allow_guest_quote_request in system.xml
     */
    const XML_PATH_GUEST_QUOTE_REQUEST = 'cart2quote_quote_form_settings/quote_form_settings/allow_guest_quote_request';

    /**
     * Path to hide_dashboard_prices in system.xml
     */
    const XML_PATH_HIDE_DASHBOARD_PRICES = 'cart2quote_advanced/general/hide_dashboard_prices';

    /**
     * Path to lock_proposal in system.xml
     */
    const XML_PATH_LOCK_PROPOSAL = 'cart2quote_advanced/general/lock_proposal';

    /**
     * Path to enable frontend quotation visibility in system.xml
     */
    const XML_PATH_FRONTEND_QUOTATION_VISIBILITY = 'cart2quote_quotation/global/enable';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Module\ModuleListInterface $moduleList
    ) {
        $this->customerSession = $customerSession;
        $this->_moduleList = $moduleList;
        parent::__construct(
            $context
        );
    }

    /**
     * @param $quote
     * @return bool
     */
    public function canChangeRequestQuote($quote)
    {
        if (!$this->isAllowed($quote->getStore())) {
            return false;
        }
        if ($this->customerSession->isLoggedIn()) {
            return $quote->canChangeRequest();
        } else {
            return true;
        }
    }

    /**
     * Check if re-request quote is allowed for given store
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        return true;
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return mixed
     */
    public function canAccept($quote)
    {
        return $quote->canAccept();
    }

    /**
     * Function to determine the current installed version of Cart2Quote
     * @return mixed
     */
    public function getCart2QuoteVersion()
    {
        $moduleCode = 'Cart2Quote_Quotation';
        $moduleInfo = $this->_moduleList->getOne($moduleCode);

        return $moduleInfo['setup_version'];
    }

    /**
     * check auto user login is turned on
     * @return boolean
     */
    public function isAutoLoginEnabled()
    {
        return $this->scopeConfig->getValue(
            self::AUTO_LOGIN_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Returns true if checkout is disabled
     * @return boolean
     */
    public function isCheckoutDisabled()
    {
        return $this->scopeConfig->getValue(
            self::CHECKOUT_DISABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * check auto confirm proposal is turned on
     * @return boolean
     */
    public function isAutoConfirmProposalEnabled()
    {
        return $this->scopeConfig->getValue(
            self::AUTO_CONFIRM_PROPOSAL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * check disable product remark field
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->scopeConfig->getValue(
            self::DISABLE_PRODUCT_REMARK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * check order references
     * @return mixed
     */
    public function getShowOrderReferences()
    {
        return $this->scopeConfig->getValue(
            self::HIDE_ORDER_REFERENCES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param null $store
     * @return bool
     */
    public function isAllowedGuestQuoteRequest(\Magento\Quote\Model\Quote $quote, $store = null)
    {
        if ($store === null) {
            $store = $quote->getStoreId();
        }

        $guestQuoteRequest = $this->scopeConfig->isSetFlag(
            self::XML_PATH_GUEST_QUOTE_REQUEST,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        if ($guestQuoteRequest == true) {
            $result = new \Magento\Framework\DataObject();
            $result->setIsAllowed($guestQuoteRequest);
            $this->_eventManager->dispatch(
                'quote_request_allow_guest',
                ['quote' => $quote, 'store' => $store, 'result' => $result]
            );

            $guestQuoteRequest = $result->getIsAllowed();
        }

        return $guestQuoteRequest;
    }

    /**
     * Config check if hide prices for dashboard
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    public function isHidePrices($quote)
    {
        $show = $this->scopeConfig->getValue(
            self::XML_PATH_HIDE_DASHBOARD_PRICES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($show) {
            return $quote->showPrices();
        }

        return true;
    }

    /**
     * Function that sets or un-sets the confirm mode based on the given value
     *
     * @param bool $value
     */
    public function setActiveConfirmMode($value)
    {
        $confirmationMode = $this->scopeConfig->getValue(
            self::XML_PATH_LOCK_PROPOSAL
        );

        if ($value && $confirmationMode) {
            $this->customerSession->setQuoteConfirmation($value);
        } else {
            $this->customerSession->setQuoteConfirmation(null);
        }
    }

    /**
     * Get locked proposal value from the session
     *
     * @return bool|null
     */
    public function getActiveConfirmMode()
    {
        return $this->customerSession->getQuoteConfirmation();
    }

    /**
     * Check Frontend Quotation Visibility setting
     *
     * @return bool
     */
    public function isFrontendEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_FRONTEND_QUOTATION_VISIBILITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
