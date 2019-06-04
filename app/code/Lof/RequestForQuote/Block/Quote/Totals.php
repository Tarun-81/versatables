<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\RequestForQuote\Block\Quote;

use Lof\RequestForQuote\Model\Quote;

/**
 * @api
 * @since 100.0.2
 */
class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * Associated array of totals
     * array(
     *  $totalCode => $totalObject
     * )
     *
     * @var array
     */
    protected $_totals;

    /**
     * @var Quote|null
     */
    protected $_quote = null;

    protected $_lof_quote = null;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize self totals and children blocks totals before html building
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->_initTotals();
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
            if (method_exists($child, 'initTotals') && is_callable([$child, 'initTotals'])) {
                $child->initTotals();
            }
        }
        return parent::_beforeToHtml();
    }

    /**
     * Get quote object
     *
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
     * Get quote object
     *
     * @return Quote
     */
    public function getLofQuote()
    {
        if ($this->_lof_quote === null) {
            if ($this->hasData('lof_quote')) {
                $this->_lof_quote = $this->_getData('lof_quote');
            } elseif ($this->_coreRegistry->registry('current_rfq_quote')) {
                $this->_lof_quote = $this->_coreRegistry->registry('current_rfq_quote');
            } elseif ($this->getParentBlock()->getLofQuote()) {
                $this->_lof_quote = $this->getParentBlock()->getLofQuote();
            }
        }
        return $this->_lof_quote;
    }

    /**
     * @param Quote $quote
     * @return $this
     */
    public function setOrder($quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Order
     */
    public function getSource()
    {
        return $this->getQuote();
    }

    /**
     * Initialize quote totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        $source = $this->getSource();

        $this->_totals = [];
        $this->_totals['subtotal'] = new \Magento\Framework\DataObject(
            ['code' => 'subtotal', 'value' => $source->getSubtotal(), 'label' => __('Subtotal')]
        );

        /**
         * Add discount
         */
		
       // if ((double)$this->getSource()->getDiscountAmount() != 0) {
            if ($this->getSource()->getDiscountDescription()) {
                $discountLabel = __('Discount (%1)', $source->getDiscountDescription());
            } else {
                $discountLabel = __('Discount');
            }
			if ($total_amt = $this->getSource()->getShippingAddress()->getTotals()) {
				$ship_amount = '';
				$tx_amt = $total_amt['tax']->getValue();
				if(isset($total_amt['shipping']) && $total_amt['shipping']){
					$ship_amount = $total_amt['shipping']->getValue();
				}
				$ship_amount = $ship_amount?(float)$ship_amount:'';
			} else {
				$ship_amount = 0;
				$tx_amt = 0;
			}
            $this->_totals['discount'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'discount',
                    'field' => 'discount_amount',
                    'value' => ($source->getGrandTotal() - $source->getSubtotal() - $ship_amount - $tx_amt),
                    'label' => $discountLabel,
                ]
            );
      //  }

        /**
         * Add tax
         */
        if ($totals = $this->getSource()->getShippingAddress()->getTotals()) {
            $taxLabel = __('Tax');
            $this->_totals['tax'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'tax',
                    'field' => 'tax_amount',
                    'value' => $totals['tax']->getValue(),
                    'label' => $taxLabel,
                ]
            );
        }

        /**
         * Add shipping
         */
        if ($totals = $this->getSource()->getShippingAddress()->getTotals()) {
            $shipping_amount = '';
            if(isset($totals['shipping']) && $totals['shipping']){
                $shipping_amount = $totals['shipping']->getValue();
            }
            $shipping_amount = $shipping_amount?(float)$shipping_amount:'';
            if ($totals['shipping']->getTitle()) {
                $shippingLabel = $totals['shipping']->getTitle();
            } else {
                $shippingLabel = __('Shipping & Handling');
            }
            $this->_totals['shipping'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'shipping',
                    'field' => 'shipping_amount',
                    'value' => $shipping_amount,
                    'label' => $shippingLabel,
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
        return $this;
    }

    /**
     * Add new total to totals array after specific total or before last total by default
     *
     * @param   \Magento\Framework\DataObject $total
     * @param   null|string $after
     * @return  $this
     */
    public function addTotal(\Magento\Framework\DataObject $total, $after = null)
    {
        if ($after !== null && $after != 'last' && $after != 'first') {
            $totals = [];
            $added = false;
            foreach ($this->_totals as $code => $item) {
                $totals[$code] = $item;
                if ($code == $after) {
                    $added = true;
                    $totals[$total->getCode()] = $total;
                }
            }
            if (!$added) {
                $last = array_pop($totals);
                $totals[$total->getCode()] = $total;
                $totals[$last->getCode()] = $last;
            }
            $this->_totals = $totals;
        } elseif ($after == 'last') {
            $this->_totals[$total->getCode()] = $total;
        } elseif ($after == 'first') {
            $totals = [$total->getCode() => $total];
            $this->_totals = array_merge($totals, $this->_totals);
        } else {
            $last = array_pop($this->_totals);
            $this->_totals[$total->getCode()] = $total;
            $this->_totals[$last->getCode()] = $last;
        }
        return $this;
    }

    /**
     * Add new total to totals array before specific total or after first total by default
     *
     * @param   \Magento\Framework\DataObject $total
     * @param   null|string $before
     * @return  $this
     */
    public function addTotalBefore(\Magento\Framework\DataObject $total, $before = null)
    {
        if ($before !== null) {
            if (!is_array($before)) {
                $before = [$before];
            }
            foreach ($before as $beforeTotals) {
                if (isset($this->_totals[$beforeTotals])) {
                    $totals = [];
                    foreach ($this->_totals as $code => $item) {
                        if ($code == $beforeTotals) {
                            $totals[$total->getCode()] = $total;
                        }
                        $totals[$code] = $item;
                    }
                    $this->_totals = $totals;
                    return $this;
                }
            }
        }
        $totals = [];
        $first = array_shift($this->_totals);
        $totals[$first->getCode()] = $first;
        $totals[$total->getCode()] = $total;
        foreach ($this->_totals as $code => $item) {
            $totals[$code] = $item;
        }
        $this->_totals = $totals;
        return $this;
    }

    /**
     * Get Total object by code
     *
     * @param string $code
     * @return mixed
     */
    public function getTotal($code)
    {
        if (isset($this->_totals[$code])) {
            return $this->_totals[$code];
        }
        return false;
    }

    /**
     * Delete total by specific
     *
     * @param   string $code
     * @return  $this
     */
    public function removeTotal($code)
    {
        unset($this->_totals[$code]);
        return $this;
    }

    /**
     * Apply sort orders to totals array.
     * Array should have next structure
     * array(
     *  $totalCode => $totalSortOrder
     * )
     *
     *
     * @param   array $quote
     * @return  $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applySortOrder($quote)
    {
        return $this;
    }

    /**
     * get totals array for visualization
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
     * Format total value based on quote currency
     *
     * @param   \Magento\Framework\DataObject $total
     * @return  string
     */
    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            return $this->getLofQuote()->formatPrice($total->getValue());
        }
        return $total->getValue();
    }
}
