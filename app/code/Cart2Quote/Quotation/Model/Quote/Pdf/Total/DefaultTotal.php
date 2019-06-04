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

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Total;

/**
 * Sales Order Total PDF model
 */
class DefaultTotal extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    /**
     * Get total for display on PDF
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $amount = $this->getSource()->formatPriceTxt($this->getAmount());

        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix() . $amount;
        }

        $title = __($this->getTitle());
        if ($this->getTitleSourceField()) {
            $label = $title . ' (' . $this->getTitleDescription() . '):';
        } else {
            $label = $title . ':';
        }

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $total = ['amount' => $amount, 'label' => $label, 'font_size' => $fontSize];
        return [$total];
    }

    /**
     * Get array of arrays with tax information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     *
     * @return array
     */
    public function getFullTaxInfo()
    {
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $taxClassAmount = $this->_taxHelper->getCalculatedTaxes($this->getQuote());
        if (!empty($taxClassAmount)) {
            foreach ($taxClassAmount as &$tax) {
                $percent = $tax['percent'] ? ' (' . $tax['percent'] . '%)' : '';
                $tax['amount'] = $this->getAmountPrefix() . $this->getQuote()->formatPriceTxt($tax['tax_amount']);
                $tax['label'] = __($tax['title']) . $percent . ':';
                $tax['font_size'] = $fontSize;
            }
        } else {
            /** @var $orders \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\Collection */
            $orders = $this->_taxOrdersFactory->create();
            $rates = $orders->loadByOrder($this->getQuote())->toArray();
            $fullInfo = $this->_taxCalculation->reproduceProcess($rates['items']);
            $tax_info = [];

            if ($fullInfo) {
                foreach ($fullInfo as $info) {
                    if (isset($info['hidden']) && $info['hidden']) {
                        continue;
                    }

                    $_amount = $info['amount'];

                    foreach ($info['rates'] as $rate) {
                        $percent = $rate['percent'] ? ' (' . $rate['percent'] . '%)' : '';

                        $tax_info[] = [
                            'amount' => $this->getAmountPrefix() . $this->getQuote()->formatPriceTxt($_amount),
                            'label' => __($rate['title']) . $percent . ':',
                            'font_size' => $fontSize,
                        ];
                    }
                }
            }
            $taxClassAmount = $tax_info;
        }

        return $taxClassAmount;
    }

    /**
     * Append empty row beneath current total
     *
     * @param $totals
     * @param $amount
     * @return array
     */
    public function appendEmptyRows($totals, $amount)
    {
        for ($i = 0; $amount > $i; $i++) {
            $totals[] = [
                'amount' => '',
                'label' => '',
                'font_size' => '',
            ];
        }
        return $totals;
    }
}
