<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Adminhtml catalog product attributes block
 */
class CheckoutAttribute extends Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_controller = 'adminhtml_checkoutAttribute';
        $this->_blockGroup = 'Aitoc_CheckoutFieldsManager';
        $this->_headerText = __('Checkout Attributes');
        $this->_addButtonLabel = __('Add Checkout Attribute');
    }
}
