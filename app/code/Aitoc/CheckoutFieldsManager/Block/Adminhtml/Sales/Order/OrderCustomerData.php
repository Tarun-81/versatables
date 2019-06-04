<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Adminhtml\Sales\Order;

use Magento\Backend\Block\Widget\Form\Container;

/**
 * Edit order address form container block
 */
class OrderCustomerData extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_customerDataCheckoutAttribute';
        $this->_blockGroup = 'Aitoc_CheckoutFieldsManager';
        parent::_construct();
        $this->addButton(
            'save',
            [
                'label' => __('Save Additional Fields'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save', 'target' => '#edit_form']],
                ]
            ],
            1
        );

        $this->addButton(
            'back',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\''
                    . $this->getUrl(
                        'sales/order/view',
                        ['order_id' => $this->getRequest()->getParam('orderid')]
                    ) . '\')',
                'class' => 'back'
            ],
            -1
        );

        $this->buttonList->remove('delete');
    }

    /**
     * Retrieve URL for save
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            'aitoccheckoutfieldsmanager/customerdatacheckoutattribute/save',
            ['_current' => true, 'back' => null]
        );
    }
}
