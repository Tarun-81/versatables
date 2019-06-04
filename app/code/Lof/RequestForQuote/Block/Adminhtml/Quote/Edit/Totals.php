<?php

namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Edit;

class Totals extends \Magento\Backend\Block\Template
{

    protected $moduleHelper;

    protected $_coreRegistry;

    public function __construct(
        \Lof\RequestForQuote\Helper\Data $moduleHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->moduleHelper = $moduleHelper;
        $this->_coreRegistry = $registry;
    }

    /**
     * @return \Lof\RequestForQuote\Helper\Data
     */
    public function getModuleHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * @param null $store
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencySymbol($store = null)
    {
        $currencySymbol = "";
        if (!$store) {
            $storeId = $this->getMageQuote()->getStoreId();
            if ($storeId !== null) {
                $store = $this->_storeManager->getStore($storeId);
            }
        }
        if ($store) {
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $currency = $_objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($store->getCurrentCurrencyCode());
            $currencySymbol = $currency->getCurrencySymbol();
        } else {
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $rfqHelper = $_objectManager->create('Lof\RequestForQuote\Helper\Data')->create();
            $currencySymbol = $rfqHelper->getCurrentCurrencySymbol();
        }


        return $currencySymbol;
    }

    public function getMageQuote()
    {
        return $this->_coreRegistry->registry('mage_quote');
    }

    /**
     * @return mixed
     */
    public function getQuote()
    {
        return $this->getParentBlock()->getQuote();
    }
}