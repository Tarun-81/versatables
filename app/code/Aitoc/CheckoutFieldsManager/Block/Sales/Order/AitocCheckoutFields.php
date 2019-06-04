<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Sales\Order;

use Aitoc\CheckoutFieldsManager\Traits\CustomFields;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Aitoc plug-in: Adding checkout fields on the invoice page
 */
class AitocCheckoutFields extends Template
{
    use CustomFields;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * AitocCheckoutFields constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->orderId = $this->getOrderId();
    }

    /**
     * Retrieve current order model instance
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->registry->registry('current_order')->getEntityId();
    }
}
