<?php

namespace Cryozonic\StripePayments\Helper;

use Psr\Log\LoggerInterface;

class Logger
{
    static $logger = null;

    public static function debug($obj)
    {
        if (!Logger::$logger)
            Logger::$logger = \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface');

        if (is_object($obj))
        {
            if (method_exists($obj, 'debug'))
                $data = $obj->debug();
            else if (method_exists($obj, 'getData'))
                $data = $obj->getData();
            else
                $data = $obj;
        }
        else
            $data = $obj;

        Logger::$logger->addDebug(print_r($data, true));
    }

    public static function log($msg)
    {
        Logger::debug($msg);
    }
}
