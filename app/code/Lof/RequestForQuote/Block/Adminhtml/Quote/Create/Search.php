<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Create;

/**
 * Adminhtml sales order create search block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Search extends \Magento\Sales\Block\Adminhtml\Order\Create
{
    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('quotation/' . $this->_controller . '/');
    }
}
