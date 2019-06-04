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

namespace Cart2Quote\Quotation\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class RenderObserver
 *
 * @package Cart2Quote\Quotation\Observer
 */
class SalableObserver implements ObserverInterface
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
     * Cart2Quote Module Enabled
     */
    protected $_enabledModule;

    /**
     * Cart2Quote Alternative Rendering Enabled
     */
    protected $_enabledAlternateRendering;

    /**
     * Module manager
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * SalableObserver constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Module\Manager $moduleManager,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_coreRegistry = $coreRegistry;
        $this->_moduleManager = $moduleManager;
        $this->quotationHelper = $quotationHelper;
        $this->_enabledAlternateRendering = !(bool)$scopeConfig->getValue(
            'cart2quote_advanced/general/disable_alternate_rendering',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_enabledModule = $moduleManager->isEnabled('Cart2Quote_Quotation');
    }

    /**
     * The function that gets executed when the event is observed
     * It registers the products that are used in situations where the salable state is checked
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_enabledModule
            || !$this->quotationHelper->isFrontendEnabled()
            || !$this->_enabledAlternateRendering
        ) {
            return;
        }

        $product = $observer->getProduct();
        $products = [];
        if ($this->_coreRegistry->registry('c2q_current_salable_product')) {
            $products = $this->_coreRegistry->registry('c2q_current_salable_product');
            $this->_coreRegistry->unregister('c2q_current_salable_product');
        }

        if ($product->getId()) {
            $products[$product->getId()] = $product;
        }

        $this->_coreRegistry->register('c2q_current_salable_product', $products);
    }
}
