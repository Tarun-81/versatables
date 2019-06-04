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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Extra;

/**
 * Class Remark
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Extra
 */
class Remark extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Grid
{

    /**
     * Get Item Id
     *
     * @return int
     */
    public function getItemId()
    {
        return $this->getItem()->getId();
    }

    /**
     * Get Quote Item
     *
     * @return \Magento\Quote\Model\Quote\Item
     * @throws \Exception
     */
    public function getItem()
    {
        if ($parentBlock = $this->getParentBlock() && $this->getParentBlock()->getItem()) {
            return $this->getParentBlock()->getItem();
        } else {
            throw new \Exception(__('Unable to load quote item on remark block'));
        }
    }

    /**
     * Get the current tier item on the quote item
     *
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    public function getTierItem()
    {
        return $this->getItem()->getCurrentTierItem();
    }

    /**
     * Get the HTML to hide the description
     *
     * @return string
     */
    public function getDescriptionDisableInput()
    {
        $html = '';

        if (!$this->getItemDescription()) {
            $html = 'disabled="disabled"';
        }

        return $html;
    }

    /**
     * Get Item Description
     *
     * @return string
     */
    public function getItemDescription()
    {
        return $this->getItem()->getDescription();
    }

    /**
     * Get hide input css
     *
     * @return string
     */
    public function getDescriptionHideInput()
    {
        $html = '';

        if (!$this->getItemDescription()) {
            $html = 'display: none';
        }

        return $html;
    }

    /**
     * Get checked html if there is an item description
     *
     * @return string
     */
    public function getDescriptionChecked()
    {
        $html = '';

        if ($this->getItemDescription()) {
            $html = 'checked';
        }

        return $html;
    }
}
