<?php

namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Edit\Shipping\Method;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form
{
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface; $priceCurrency
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Registry $registry,
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $taxData, $data);
    }

    public function getAddress()
    {
        return $this->getMageQuote()->getShippingAddress();
    }

    public function getMageQuote(){
        return $this->_coreRegistry->registry('mage_quote');
    }

    /**
     * Retrieve array of shipping rates groups
     *
     * @return array
     */
    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $this->_rates = $this->getAddress()->getGroupedAllShippingRates();
        }
        return $this->_rates;
    }
}
