<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel;

use Aitoc\CheckoutFieldsManager\Model\Spi\OrderCustomerDataResourceInterface;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class OrderCustomerData extends AbstractDb implements OrderCustomerDataResourceInterface
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'value_id';

    /**
     * Define main table.
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('aitoc_sales_order_value', 'value_id');
    }
}
