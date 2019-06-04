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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * Adminhtml quotation quote create order button
 */
class CreateOrder extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_onClickCode;

    /**
     * Get buttons html
     * @return string
     */
    public function getButtonsHtml()
    {
        $addButtonData = [
            'label' => __('Create Order'),
            'onclick' => $this->_onClickCode,
            'class' => 'action-add primary create-order'
        ];
        return $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            $addButtonData
        )->toHtml();
    }

    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        //set the javascript for the onClick buttons
        $this->_onClickCode = ' var actionUrl = jQuery("#edit_form").attr("action");
                                jQuery("#edit_form").attr("action","' . $this->getSaveUrl() . '");
                                quote.submit();
                                jQuery("#edit_form").attr("action",actionUrl);';

        //construct this Block
        parent::_construct();
        $this->setId('quotation_quote_view_create_order');
    }

    /**
     * Retrieve url for form submitting
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('quotation/quote/convert');
    }
}
