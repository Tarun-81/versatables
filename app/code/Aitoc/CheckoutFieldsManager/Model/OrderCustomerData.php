<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model;

use Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class OrderCustomerData extends AbstractCustomerData implements OrderCustomerDataInterface
{
    /**
     * Name of the module.
     */
    const MODULE_NAME = 'Aitoc_CheckoutFieldsManager';

    const TYPE = 'aitoc_checkoutfieldsmanager_ordercustomerdata';

    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'value_id';

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData');
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->getData(self::KEY_ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        $this->setData(self::KEY_ORDER_ID, $orderId);

        return $this;
    }
}
