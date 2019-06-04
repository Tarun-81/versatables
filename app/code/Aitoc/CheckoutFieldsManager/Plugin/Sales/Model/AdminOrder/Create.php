<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Plugin\Sales\Model\AdminOrder;

use Magento\Framework\Registry;
use Magento\Sales\Model\AdminOrder\Create as AdminOrderCreate;

class Create
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Save constructor.
     *
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param AdminOrderCreate $object
     * @param $order
     * @return mixed
     */
    public function afterCreateOrder(AdminOrderCreate $object, $order)
    {
        if ($order->getId()) {
            $this->registry->register('current_order', $order);
        }

        return $order;
    }
}
