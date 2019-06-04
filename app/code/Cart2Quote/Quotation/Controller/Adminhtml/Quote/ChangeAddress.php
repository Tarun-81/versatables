<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

/**
 * Class Hold
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class ChangeAddress extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * View quote detail
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Quote\Model\Quote\Address $quoteAddress */
        $quoteAddress = $this->_objectManager->create('Magento\Quote\Model\Quote\Address');
        /** @var \Magento\Customer\Api\AddressRepositoryInterface $addressService */
        $addressService = $this->_objectManager->create('Magento\Customer\Api\AddressRepositoryInterface');
        /** @var \Cart2Quote\Quotation\Model\Quote $quote */
        $quote = $this->_initQuote();

        if ($quote) {
            $addressType = $this->getRequest()->getParam('address_type');

            if ($addressType == 'billing') {
                $defaultBillingAddressId = $quote->getCustomer()->getDefaultBilling();
                if (isset($defaultBillingAddressId) && !empty($defaultBillingAddressId)) {
                    $quoteAddress->importCustomerAddressData($addressService->getById($defaultBillingAddressId));
                    $quote->setBillingAddress($quoteAddress);
                    $quote->setRecollect(true);
                    $quote->saveQuote();
                }
            }

            if ($addressType == 'shipping') {
                $defaultShippingAddressId = $quote->getCustomer()->getDefaultShipping();
                if (isset($defaultShippingAddressId) && !empty($defaultShippingAddressId)) {
                    $quoteAddress->importCustomerAddressData($addressService->getById($defaultShippingAddressId));
                    $quote->setShippingAddress($quoteAddress);
                    $quote->setRecollect(true);
                    $quote->saveQuote();
                }
            }

            return $this->resultRedirectFactory->create()->setPath(
                'quotation/quote/view',
                ['quote_id' => $quote->getId()]
            );
        }

        return $this->resultRedirectFactory->create()->setPath('quotation/quote/index');
    }
}
