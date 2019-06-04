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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote;

/**
 * Class Create
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote
 */
class Create extends \Magento\Sales\Block\Adminhtml\Order\Create
{
    /**
     * Prepare header html
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        $out = '<div id="order-header">' . $this->getLayout()->createBlock(
                \Cart2Quote\Quotation\Block\Adminhtml\Quote\Create\Header::class
            )->toHtml() . '</div>';
        return $out;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->update('save', 'label', __('Create Quote'));
    }

    /**
     * {@inheritdoc}
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $pageTitle = $this->getLayout()->createBlock(
            \Cart2Quote\Quotation\Block\Adminhtml\Quote\Create\Header::class
        )->toHtml();
        if (is_object($this->getLayout()->getBlock('page.title'))) {
            $this->getLayout()->getBlock('page.title')->setPageTitle($pageTitle);
        }
        return \Magento\Backend\Block\Widget\Form\Container::_prepareLayout();
    }
}
