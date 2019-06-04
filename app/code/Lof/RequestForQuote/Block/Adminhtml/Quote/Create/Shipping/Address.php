<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Create\Shipping;

/**
 * Adminhtml sales order create billing address block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 * @since 100.0.2
 */

class Address extends \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Address
{
    public function getAddress()
    {
        if ($this->getIsAsBilling()) {
            $address = $this->getCreateOrderModel()->getBillingAddress();
        } else {
            $address = $this->getCreateOrderModel()->getShippingAddress();
        }
        return $address;
    }

    public function getIsAsBilling()
    {
        return $this->getCreateOrderModel()->getShippingAddress()->getSameAsBilling();
    }

    /**
     * Retrieve create order model object
     *
     * @return \Lof\RequestForQuote\Model\AdminOrder\Create
     */
    public function getCreateOrderModel()
    {
        return $this->_getSession()->getRfqQuote();
    }
}
