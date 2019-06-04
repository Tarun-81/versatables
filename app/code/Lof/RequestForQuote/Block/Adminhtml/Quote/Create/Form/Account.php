<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Create\Form;

/**
 * Create order account form
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Account extends \Magento\Sales\Block\Adminhtml\Order\Create\Form\Account
{
        /**
     * Retrieve quote model object
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_getSession()->getRfqQuote();
    }
}
