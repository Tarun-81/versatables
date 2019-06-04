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

namespace Cart2Quote\Quotation\Block\Quote\Email\Items;

use Magento\Quote\Model\Quote\Item as QuoteItem;
use Cart2Quote\Quotation\Model\Quote\TierItemFrontend;

/**
 * Quote Email items default renderer
 *
 * Class DefaultItems
 * @package Cart2Quote\Quotation\Block\Quote\Email\Items
 */
class DefaultItems extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * @var \Cart2Quote\Quotation\Block\Quote\TierItem
     */
    protected $tierItemBlock;

    /**
     * DefaultItems constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param \Cart2Quote\Quotation\Block\Quote\TierItem
     * @param array $data \
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        \Cart2Quote\Quotation\Block\Quote\TierItem $tierItemBlock,
        array $data = []
    ) {
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        $this->tierItemBlock = $tierItemBlock;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve current quote model instance
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->getItem()->getQuote();
    }

    /**
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getQuoteItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * @param string|array $value
     * @return string
     */
    public function getValueHtml($value)
    {
        if (is_array($value)) {
            return sprintf(
                    '%d',
                    $value['qty']
                ) . ' x ' . $this->escapeHtml(
                    $value['title']
                ) . " " . $this->getItem()->getQuote()->formatPrice(
                    $value['price']
                );
        } else {
            return $this->escapeHtml($value);
        }
    }

    /**
     * @param mixed $item
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getQuoteItem()->getProductOptionByCode('simple_sku')) {
            return $item->getQuoteItem()->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }

    /**
     * Return product additional information block
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    /**
     * Get the html for item price
     * @param QuoteItem $item
     * @return string
     */
    public function getItemPrice($item)
    {
        $block = $this->getLayout()->getBlock('item_row_total');
        $block->setItem($item);
        return $block->toHtml();
    }

    /**
     * Check disabled product remark field
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->cart2QuoteHelper->isProductRemarkDisabled();
    }

    /**
     * Get tier item quantity
     *
     * @return string
     */
    public function getTierQtyHtml()
    {
        return $this->tierItemBlock->getTierQty($this->getItem());
    }

    /**
     * Get tier item custom price
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
}
