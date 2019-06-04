<?php

namespace Cryozonic\StripeSubscriptions\Model;

use Cryozonic\StripePayments\Helper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public static $moduleName           = "Stripe Subscriptions M2";
    public static $moduleVersion        = "1.4.0";
    public static $moduleUrl            = "https://store.cryozonic.com/magento-2/stripe-subscriptions.html";

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Helper\Generic $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    public static function module()
    {
        return self::$moduleName . " v" . self::$moduleVersion;
    }

}
