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

namespace Cart2Quote\Quotation\Plugin\Magento\Framework\View\Layout;

/**
 * Class ScheduledStructure
 * @package Cart2Quote\Quotation\Plugin\Magento\Framework\View\Layout
 */
class ScheduledStructure
{
    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Cart2Quote Alternative Rendering Enabled
     */
    protected $_enabledAlternateRendering;

    /**
     * Temp check var
     * @var
     */
    protected $_isMiniCartRendered = false;

    /**
     * Temp useCache var
     * @var
     */
    protected $_useCache = true;
    /**
     * Temp counter var
     * @var
     */
    protected $_counter = 0;

    /**
     * ScheduledStructure constructor.
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->quotationHelper = $quotationHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_enabledAlternateRendering = !(bool)$scopeConfig->getValue(
            'cart2quote_advanced/general/disable_alternate_rendering',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Cancel the move of miniquote to header-wrapper when header-wrapper is removed in xml
     * Or when alternative rendering mode is enabled
     *
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function afterGetListToMove($subject, $result)
    {
        if (!$this->quotationHelper->isFrontendEnabled()) {
            return $result;
        }

        $sheduledRemoves = $subject->getListToRemove();
        $sheduledRemoveKey = array_search('header-wrapper', $sheduledRemoves);

        if ($sheduledRemoveKey || $this->_enabledAlternateRendering) {
            $moveKey = array_search('miniquote', $result);
            if ($moveKey) {
                unset($result[$moveKey]);
            }
        }

        return $result;
    }
}
