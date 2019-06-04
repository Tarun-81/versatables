<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuotePdf\Block\Adminhtml\Quote\QuotePdf;

use Magento\Customer\Model\Context;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class Items extends \Magento\Framework\View\Element\Template
{
     /**
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $configurationHelper;
    protected $currencySymbol = null;
    protected $_quoteAddress;

    protected $_quote_field_data = null;

    protected $_quote_extra_field_data = null;

    protected $_quote_address = null;

    protected $_quote_billing_address = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Block\Data $directoryData,
        CustomerRepository $customerRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Helper\Product\Configuration $configurationHelper,
        array $data = []
    ) {
        $this->_coreRegistry      = $coreRegistry;
        parent::__construct($context, $data);
        $this->_directoryData     = $directoryData;
        $this->customerRepository = $customerRepository;
        $this->urlHelper          = $urlHelper;
        $this->configurationHelper = $configurationHelper;
    }
    public function getQuote(){
        if($this->getData("quote")){
            return $this->getData("quote");
        } else {
            return $this->getParentBlock()->getQuote();
        }
    }
    public function getMageQuote(){
        if($this->getData("mage_quote")){
            return $this->getData("mage_quote");
        } else {
            return $this->getParentBlock()->getMageQuote();
        }
    }
    public function getCurrencySymbol($store = null) {
        if(!$this->currencySymbol) {
            $this->currencySymbol = [];
            if(!$store) {
                $storeId = $this->getMageQuote()->getStoreId();
                if ($storeId !== null) {
                    $store = $this->_storeManager->getStore($storeId);
                }
            }
            if($store) {
                $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $currency = $_objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($store->getCurrentCurrencyCode());
                $this->currencySymbol = $currency->getCurrencySymbol();
            } else {
                $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $rfqHelper                = $_objectManager->create('Lof\RequestForQuote\Helper\Data')->create();
                $this->currentcySymbol          = $rfqHelper->getCurrentCurrencySymbol();  
            }
        }
        return $this->currencySymbol;
    }
    /**
     * Retrieve order items collection
     *
     * @return Collection
     */
    public function getItemsCollection()
    {
        return $this->getMageQuote()->getItemsCollection();
    }
    public function getSelectedOptionsOfQuoteItem($item)
    {
        return $this->configurationHelper->getCustomOptions($item);
    }
}
