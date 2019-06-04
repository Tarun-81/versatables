<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Helper;

use Magento\Framework\App\ObjectManager;

/**
 * To hide difference in 100.1 and 100.2 validation rules serialization.
 *
 * Class Serializer
 * @package Aitoc\CheckoutFieldsManager\Helper
 */
class Serializer
{
    const MAGENTO_SERIALIZER_CLASS = 'Magento\Framework\Serialize\Serializer\Json';

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $magentoSerializer;

    public function __construct()
    {
        if (class_exists(self::MAGENTO_SERIALIZER_CLASS)) {
            $this->magentoSerializer = ObjectManager::getInstance()->create(self::MAGENTO_SERIALIZER_CLASS);
        }
    }

    /**
     * Serialize
     *
     * If core json serializer available try to use them. Otherwise serialize() used.
     *
     * @param $value
     * @return string
     */
    public function serialize($value)
    {
        if ($this->magentoSerializer) {
            return $this->magentoSerializer->serialize($value);
        }

        return serialize($value);
    }

    /**
     * Unserialize
     *
     * If core json serializer available try to use them. If exception thrown then unserialize() used.
     * Otherwise unserialize() used.
     *
     * @param $string
     * @return mixed
     */
    public function unserialize($string)
    {
        if ($this->magentoSerializer) {
            try {
                return $this->magentoSerializer->unserialize($string);
            } catch (\InvalidArgumentException $exception) {
                return unserialize($string);
            }
        }

        return unserialize($string);
    }
}
