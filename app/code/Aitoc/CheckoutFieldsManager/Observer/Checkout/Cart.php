<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Observer\Checkout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

/**
 * Event name: checkout_cart_save_before
 * Observer name: aitoc_ignore_validate_additional_fields
 */
class Cart implements ObserverInterface
{
    /** @var Registry */
    private $registry;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * Backend validation is not forking for now, but should be in v.2 and this code need only for backend validation
     *
     * Avoid additional checkout fields validation in cart (also add\delete product to cart)
     *
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        if ($this->registry->registry('cfm_ignore_validation') === null) {
            $this->registry->register('cfm_ignore_validation', true);
        }
    }
}
