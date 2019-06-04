<?php

namespace Cryozonic\StripeExpress\Model;

class Config
{
    public static $moduleName           = "Stripe Express M2";
    public static $moduleVersion        = "1.2.0";
    public static $moduleUrl            = "https://store.cryozonic.com/magento-2/stripe-express.html";

    public static function module()
    {
        return self::$moduleName . " v" . self::$moduleVersion;
    }
}
