<?php

namespace BetterDeals\ZopimChat\Block;

use Magento\Framework\View\Element\Template;

class Chat extends Template
{
    const XML_PATH_GENERAL_WIDGET_CODE = 'zopim/general/widget_code';
    const XML_PATH_GENERAL_DEFAULT = 'zopim/general/default';
    const XML_PATH_GENERAL_OVERRIDE = 'zopim/general/override';

    const XML_PATH_COOKIE_LAW_COMPLY = 'zopim/cookie_law/comply';
    const XML_PATH_COOKIE_LAW_COMPLY_EXPLICIT = 'zopim/cookie_law/comply_explicit';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Helper
     * @var \BetterDeals\ZopimChat\Helper\Chat
     */
    protected $helper;

    public function __construct (
        Template\Context $context,
        \BetterDeals\ZopimChat\Helper\Chat $helper,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    public function isEnabled()
    {
        return $this->helper->isSetFlag(self::XML_PATH_GENERAL_DEFAULT) xor array_intersect(
            $this->getLayout()->getUpdate()->getHandles(),
            array_map('trim', explode(PHP_EOL, $this->helper->getConfig(self::XML_PATH_GENERAL_OVERRIDE)))
        );
    }

    public function getWidgetCode()
    {
        return $this->helper->getConfig(self::XML_PATH_GENERAL_WIDGET_CODE);
    }

    public function useCustomerData()
    {
        return $this->helper->useCustomerData();
    }

    public function useCustomerName()
    {
        return $this->helper->useCustomerName();
    }

    public function useCustomerEmail()
    {
        return $this->helper->useCustomerEmail();
    }

    public function useCustomerTelephone()
    {
        return $this->helper->useCustomerTelephone();
    }

    public function useCustomerOrders()
    {
        return $this->helper->useCustomerOrders();
    }

    public function getCookieLawComply()
    {
        return $this->helper->isSetFlag(self::XML_PATH_COOKIE_LAW_COMPLY);
    }

    public function getCookieLawComplyExplicit()
    {
        return $this->helper->isSetFlag(self::XML_PATH_COOKIE_LAW_COMPLY_EXPLICIT);
    }
}