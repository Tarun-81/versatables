<?php
/**
 *
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2017] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 */

namespace Cart2Quote\AutoProposal\Model\System\Config;

/**
 * Class AutoProposalRanges
 *
 * @package Cart2Quote\AutoProposal\Model\System\Config
 */
class AutoProposalRanges extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    /**
     * Default config values
     *
     * @var array
     */
    private $defaultValues = [
        \Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISCOUNT_IDENTIFIER => false,
        \Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISABLE_AUTOPROPOSAL_IDENTIFIER => false,
        \Cart2Quote\AutoProposal\Api\Data\RangeInterface::ENABLE_SHIPPING_IDENTIFIER => false,
        \Cart2Quote\AutoProposal\Api\Data\RangeInterface::SHIPPING_AMOUNT_IDENTIFIER => false,
        \Cart2Quote\AutoProposal\Api\Data\RangeInterface::NOTIFY_SALESREP_IDENTIFIER => false,
        \Cart2Quote\AutoProposal\Api\Data\RangeInterface::MIN_VALUE_IDENTIFIER => 0,
        \Cart2Quote\AutoProposal\Api\Data\RangeInterface::MAX_VALUE_IDENTIFIER => false
    ];

    /**
     * @return \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
     */
    public function beforeSave()
    {
        $values = $this->getValue();
        $this->prepareValues($values);
        $this->sortValues($values);
        $this->setValue($values);

        return parent::beforeSave();
    }

    /**
     * @param string $values
     */
    private function sortValues(&$values)
    {
        usort($values, [$this, 'compare']);
    }

    /**
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function compare($a, $b)
    {
        if ($a['min_value'] == $b['min_value']) {
            return 0;
        }

        return ($a['min_value'] < $b['min_value']) ? -1 : 1;
    }


    /**
     * @param $values
     *
     * @return array
     */
    private function prepareValues(&$values)
    {
        foreach ($values as $id => &$value) {
            if (!is_array($value)) {
                unset($values[$id]);
                continue;
            }
            if (empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::MIN_VALUE_IDENTIFIER])) {
                unset($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::MIN_VALUE_IDENTIFIER]);
            }
            if (empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::MAX_VALUE_IDENTIFIER])) {
                unset($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::MAX_VALUE_IDENTIFIER]);
            }
            if (empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISCOUNT_IDENTIFIER])) {
                unset($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISCOUNT_IDENTIFIER]);
            }
            if (empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::SHIPPING_AMOUNT_IDENTIFIER])) {
                unset($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::SHIPPING_AMOUNT_IDENTIFIER]);
            }
            if (!empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISABLE_AUTOPROPOSAL_IDENTIFIER])) {
                $value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISABLE_AUTOPROPOSAL_IDENTIFIER] = (bool)$value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISABLE_AUTOPROPOSAL_IDENTIFIER];
            }
            if (!empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::ENABLE_SHIPPING_IDENTIFIER])) {
                $value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::ENABLE_SHIPPING_IDENTIFIER] = (bool)$value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::ENABLE_SHIPPING_IDENTIFIER];
            }
            if (!empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::NOTIFY_SALESREP_IDENTIFIER])) {
                $value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::NOTIFY_SALESREP_IDENTIFIER] = (bool)$value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::NOTIFY_SALESREP_IDENTIFIER];
            }
            $value = array_merge($this->defaultValues, $value);
        }
    }

    /**
     * @return void
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $values = $this->getValue();
        if (is_array($values)) {
            foreach ($values as $id => &$value) {
                if (!empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISABLE_AUTOPROPOSAL_IDENTIFIER])) {
                    $value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::DISABLE_AUTOPROPOSAL_IDENTIFIER] = 'checked="checked"';
                }
                if (!empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::NOTIFY_SALESREP_IDENTIFIER])) {
                    $value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::NOTIFY_SALESREP_IDENTIFIER] = 'checked="checked"';
                }
                if (!empty($value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::ENABLE_SHIPPING_IDENTIFIER])) {
                    $value[\Cart2Quote\AutoProposal\Api\Data\RangeInterface::ENABLE_SHIPPING_IDENTIFIER] = 'checked="checked"';
                }
            }
        }
        $this->setValue($values);
    }
}
