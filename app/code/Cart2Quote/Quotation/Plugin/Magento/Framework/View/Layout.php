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

namespace Cart2Quote\Quotation\Plugin\Magento\Framework\View;

/**
 * Class Layout
 * @package Cart2Quote\Quotation\Plugin\Magento\Framework\View
 */
class Layout
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
     * Layout constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->quotationHelper = $quotationHelper;
        $this->_enabledAlternateRendering = !(bool)$scopeConfig->getValue(
            'cart2quote_advanced/general/disable_alternate_rendering',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $subject
     * @param string $alias
     * @param bool $useCache
     * @return array
     */
    public function beforeRenderElement(
        $subject,
        $alias = '',
        $useCache = true
    ) {
        if (!$this->quotationHelper->isFrontendEnabled() || !$this->_enabledAlternateRendering) {
            return [$alias, $useCache];
        }

        if ($alias == 'minicart') {
            $this->_isMiniCartRendered = true;
            $this->_useCache = $useCache;
        } else {
            if ($this->_isMiniCartRendered) {
                //count the nesting
                $this->_counter++;
            }
        }
    }

    /**
     * @param $subject
     * @param $result
     * @return string
     */
    public function afterRenderElement(
        $subject,
        $result
    ) {
        if ($this->quotationHelper->isFrontendEnabled()
            && $this->_enabledAlternateRendering
            && $this->_isMiniCartRendered
        ) {
            //check for nesting
            if ($this->_counter != 0) {
                $this->_counter--;
                return $result;
            }

            $this->_isMiniCartRendered = false;
            $miniquote = $subject->renderElement('miniquote', $this->_useCache);
            return $result . $miniquote;
        }

        return $result;
    }
}
