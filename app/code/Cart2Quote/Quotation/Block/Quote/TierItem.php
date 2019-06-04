<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */
namespace Cart2Quote\Quotation\Block\Quote;

/**
 * Class TierItem
 * @package Cart2Quote\Quotation\Block\Quote
 */
class TierItem
{
    /**
     * Get tier item quantity
     *
     * @return string
     */
    public function getTierQty($item)
    {
        $data = $this->getTierItemAttributeData($item, "qty");
        foreach ($data as $key => $qty) {
            $qtys[$key] = $qty * 1;
        }

        return $this->getTierHtml($item, $qtys);
    }
    /**
     * Get tier item custom price
     *
     * @return string
     */
    public function getTierCustomPrice($item)
    {
        $data = $this->getTierItemAttributeData($item, "custom_price");
        $customPrices = array();
        if (!empty($data)) {
            foreach ($data as $key => $customPrice) {
                $customPrices[$key] = $item->getQuote()->formatPrice($customPrice);
            }
        }

        return $this->getTierHtml($item, $customPrices);
    }

    /**
     * Get tier item row total
     *
     * @return string
     */
    public function getTierRowTotal($item)
    {
        $data = $this->getTierItemAttributeData($item, "row_total");
        foreach ($data as $key => $rowTotal) {
            $rowTotals[$key] = $item->getQuote($item)->formatPrice($rowTotal);
        }

        return $this->getTierHtml($item, $rowTotals);
    }

    /**
     * Get the selected attribute data from tier item
     *
     * @param $attribute
     * @return array
     */
    public function getTierItemAttributeData($item, $attribute)
    {
        $tierItems = $item->getTierItems();
        $data = array();
        if (isset($tierItems)) {
            foreach ($tierItems as $tierItem) {
                $data[$tierItem->getId()] = $tierItem->getData($attribute);
            }
        }

        return $data;
    }

    /**
     * Generate the tier item html
     *
     * @param $tierItemAttribute
     * @return string
     */
    public function getTierHtml($item, $tierItemAttribute)
    {
        $tierHtml = '<br/>';
        if (!empty($tierItemAttribute)) {
            foreach ($tierItemAttribute as $key => $tierItem) {
                if (!$this->isTierSelected($item, $key)) {
                    $tierHtml .= sprintf("%s <br/>", $tierItem);
                }
            }
        }

        return $tierHtml;
    }

    /**
     * Is tier selected
     *
     * @param $tierId
     * @return bool
     */
    public function isTierSelected($item, $tierId)
    {
        return $item->getCurrentTierItem()->getId() == $tierId;
    }
}
