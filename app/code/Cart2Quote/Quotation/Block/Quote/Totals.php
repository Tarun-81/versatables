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

use Cart2Quote\Quotation\Model\Quote;

/**
 * Class Totals
 * @package Cart2Quote\Quotation\Block\Quote
 */
class Totals extends \Magento\Sales\Block\Order\Totals
{
    const XML_PATH_CART2QUOTE_QUOTATION_GLOBAL_SHOW_QUOTE_ADJUSTMENT = 'cart2quote_advanced/general/show_quote_adjustment';

    /**
     * @var Quote|null
     */
    protected $_quote = null;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Quote|null
     */
    protected $_source = null;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * Totals constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry ,
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param array $data \
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        array $data = []
    ) {
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct(
            $context,
            $registry,
            $data
        );
    }

    /**
     * Format total value based on quote currency
     * @param   \Magento\Framework\DataObject $total
     * @return  string
     */
    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            return $this->getQuote()->formatPrice($total->getValue());
        }

        return $total->getValue();
    }

    /**
     * Get quote object
     * @return Quote
     */
    public function getQuote()
    {
        if ($this->_quote === null) {
            if ($this->hasData('quote')) {
                $this->_quote = $this->_getData('quote');
            } elseif ($this->_coreRegistry->registry('current_quote')) {
                $this->_quote = $this->_coreRegistry->registry('current_quote');
            } elseif ($this->getParentBlock()->getQuote()) {
                $this->_quote = $this->getParentBlock()->getQuote();
            }
        }

        return $this->_quote;
    }

    /**
     * @param Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Get totals array for visualization
     *
     * @param array|null $area
     * @return array
     */
    public function getTotals($area = null)
    {
        $totals = [];
        if ($area === null) {
            $totals = $this->_totals;
        } else {
            $area = (string)$area;
            foreach ($this->_totals as $total) {
                $totalArea = (string)$total->getArea();
                if ($totalArea == $area) {
                    $totals[] = $total;
                }
            }
        }
        return $totals;
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
     * Has Optional Products
     *
     * @return bool
     */
    public function hasOptionalProducts()
    {
        return $this->getParentBlock() &&
            $this->getParentBlock()->hasOptionalProducts();
    }

    /**
     * Initialize quote totals array
     * @return $this
     */
    protected function _initTotals()
    {
        $source = $this->getSource();

        $this->_totals = [];

        $showSubtotal = false;

        switch ($this->_scopeConfig->getValue(self::XML_PATH_CART2QUOTE_QUOTATION_GLOBAL_SHOW_QUOTE_ADJUSTMENT)) {
            case '1':
                $showSubtotal = true;
                break;
            case '2':
                $showSubtotal = $source->getOriginalSubtotal() && ($source->getSubtotal() - $source->getOriginalSubtotal()) != 0;
                break;
        }

        if ($showSubtotal) {
            $this->_totals['original_subtotal'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'original_subtotal',
                    'value' => $source->getOriginalSubtotal(),
                    'label' => __('Original Subtotal')
                ]
            );

            $this->_totals['quote_adjustment'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'quote_adjustment',
                    'value' => ($source->getSubtotal() - $source->getOriginalSubtotal()),
                    'label' => __('Quote adjustment')
                ]
            );
        }

        $this->_totals['subtotal'] = new \Magento\Framework\DataObject(
            ['code' => 'subtotal', 'value' => $source->getSubtotal(), 'label' => __('Subtotal')]
        );

        /**
         * Add shipping
         */
        if (!$source->getIsVirtual() && ((double)$source->getShippingAmount() || $source->getShippingDescription())) {
            $this->_totals['shipping'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'shipping',
                    'field' => 'shipping_amount',
                    'value' => $this->getSource()->getShippingAmount(),
                    'label' => __('Shipping & Handling'),
                ]
            );
        }

        /**
         * Add discount
         */
        if ((double)$this->getSource()->getDiscountAmount() != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = __('Discount (%1)', $source->getDiscountDescription());
            } else {
                $discountLabel = __('Discount');
            }
            $this->_totals['discount'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'discount',
                    'field' => 'discount_amount',
                    'value' => $source->getDiscountAmount(),
                    'label' => $discountLabel,
                ]
            );
        }

        $this->_totals['grand_total'] = new \Magento\Framework\DataObject(
            [
                'code' => 'grand_total',
                'field' => 'grand_total',
                'strong' => true,
                'value' => $source->getGrandTotal(),
                'label' => __('Grand Total'),
            ]
        );

        /**
         * Base grandtotal
         */
        if ($this->getQuote()->isCurrencyDifferent()) {
            $this->_totals['base_grandtotal'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'base_grandtotal',
                    'value' => $this->getQuote()->formatBasePrice($source->getBaseGrandTotal()),
                    'label' => __('Grand Total to be Charged'),
                    'is_formated' => true,
                ]
            );
        }

        return $this;
    }

    /**
     * Get totals source object
     * @return Quote
     */
    public function getSource()
    {
        if ($this->_source == null) {
            $quote = $this->getQuote();

            //make a clone to make sure that this merged object is never saved
            $tmpQuote = clone $this->getQuote();

            //a merged object is expected in the tax block
            if ($tmpQuote->isVirtual()) {
                $tmpQuote->addData($this->getQuote()->getBillingAddress()->getData());
            } else {
                $tmpQuote->addData($this->getQuote()->getShippingAddress()->getData());
            }

            //make sure we never lose the customer id
            if ($quote->getCustomerId() != $tmpQuote->getCustomerId()) {
                if ($quote->getCustomerId()) {
                    $tmpQuote->setCustomerId($quote->getCustomerId());
                }
            }

            $this->_source = $tmpQuote;
        }

        return $this->_source;
    }
}
