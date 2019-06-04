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
namespace Cart2Quote\Quotation\Block\Quote\Item\Renderer;

/**
 * Class Column
 * @package Cart2Quote\Quotation\Block\Quote\Item\Renderer
 */
class Column extends DefaultRenderer
{
    /**
     * Get the item from the parent block
     *
     * @return \Magento\Quote\Model\Quote\Item
     * @throws \Exception
     */
    public function getItem()
    {
        if ($parentBlock = $this->getParentBlock()) {
            return $parentBlock->getItem();
        } else {
            throw new \Exception('Undefined quote item in block ' . $this->getNameInLayout());
        }
    }

    /**
     * Get tier item quantity html
     *
     * @return string
     */
    public function getTierQtyHtml()
    {
        return $this->tierItemBlock->getTierQty($this->getItem());
    }

    /**
     * Get tier item custom price html
     *
     * @return string
     */
    public function getTierCustomPriceHtml()
    {
        return $this->tierItemBlock->getTierCustomPrice($this->getItem());
    }

    /**
     * Get tier item row total
     *
     * @return string
     */
    public function getTierRowTotalHtml()
    {
        return $this->tierItemBlock->getTierRowTotal($this->getItem());
    }

    /**
     * Get selected tier radio button html
     *
     * @return string
     */
    public function getSelectedRadiobuttonHtml()
    {
        return sprintf(
            "<input checked type='radio' class='qty-tier' name='%s' value='%s'>",
            $this->getItem()->getId(), $this->getItem()->getCurrentTierItem()->getId()
        );
    }

    /**
     * Get tier radio buttons html
     *
     * @return string
     */
    public function getTierRadiobuttonsHtml()
    {
        $tierHtml = '<br/><br/>';
        $item = $this->getItem();
        $tierItems = $item->getTierItems();

        if (isset($tierItems)) {
            foreach ($tierItems as $tierItemId => $tierItem) {
                if(!$this->tierItemBlock->isTierSelected($item, $tierItem->getId()))
                    $tierHtml .= sprintf(
                        "<input type='radio' class='qty-tier' name='%s' value='%s'><br/>",
                        $this->getItem()->getId(), $tierItem->getId()
                    );
            }
        }

        return $tierHtml;
    }

    /**
     * Get config setting for hide prices dashboard
     *
     * @return bool
     */
    public function isHidePrices()
    {
        return $this->quotationHelper->isHidePrices($this->getQuote());
    }
}
