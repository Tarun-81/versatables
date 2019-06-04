<?php

namespace Cart2Quote\AutoProposal\Model;

/**
 * Class Range
 * @package Cart2Quote\AutoProposal\Model
 *
 */
class Range extends \Magento\Framework\Model\AbstractModel implements \Cart2Quote\AutoProposal\Api\Data\RangeInterface
{
    /**
     * @return bool
     */
    public function getDisableAutoProposal()
    {
        return (bool)$this->getData(self::DISABLE_AUTOPROPOSAL_IDENTIFIER);
    }

    /**
     * @return bool
     */
    public function getNotifySalesrep()
    {
        return (bool)$this->getData(self::NOTIFY_SALESREP_IDENTIFIER);
    }

    /**
     * @return float|bool
     */
    public function getDiscount()
    {
        return $this->getData(self::DISCOUNT_IDENTIFIER);
    }

    /**
     * @return bool
     */
    public function getEnableShipping()
    {
        return (bool)$this->getData(self::ENABLE_SHIPPING_IDENTIFIER);
    }

    /**
     * @return float|bool
     */
    public function getShippingAmount()
    {
        return $this->getData(self::SHIPPING_AMOUNT_IDENTIFIER);
    }

    /**
     * @return float
     */
    public function getMinValue()
    {
        return $this->getData(self::MIN_VALUE_IDENTIFIER);
    }

    /**
     * @return float
     */
    public function getMaxValue()
    {
        return $this->getData(self::MAX_VALUE_IDENTIFIER);
    }
}