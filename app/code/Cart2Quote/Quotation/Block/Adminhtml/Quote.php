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

namespace Cart2Quote\Quotation\Block\Adminhtml;

/**
 * Adminhtml quotation quotes block
 */
class Quote extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Retrieve url for order creation
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('quotation/quote_create/start');
    }

    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_quote';
        $this->_blockGroup = 'Cart2Quote_Quotation';
        $this->_headerText = __('Quotes');
        $this->_addButtonLabel = __('Create New Quote');
        parent::_construct();
        if (!$this->_authorization->isAllowed('Cart2Quote_Quotation::create')) {
            $this->buttonList->remove('add');
        }
    }
}
