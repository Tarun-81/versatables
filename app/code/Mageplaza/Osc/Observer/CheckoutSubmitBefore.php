<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Osc\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\CustomerManagement;

/**
 * Class CheckoutSubmitBefore
 * @package Mageplaza\Osc\Observer
 */
class CheckoutSubmitBefore implements ObserverInterface
{
	/**
	 * @var \Magento\Checkout\Model\Session
	 */
	protected $checkoutSession;

	/**
	 * @type \Magento\Framework\DataObject\Copy
	 */
	protected $_objectCopyService;

	/**
	 * @type \Magento\Framework\Api\DataObjectHelper
	 */
	protected $dataObjectHelper;

	/**
	 * @type \Magento\Customer\Api\AccountManagementInterface
	 */
	protected $accountManagement;

	/**
	 * @var CustomerManagement
	 */
	protected $customerManagement;

	/**
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Framework\DataObject\Copy $objectCopyService
	 * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
	 * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
	 * @param \Magento\Quote\Model\CustomerManagement $customerManagement
	 */
	public function __construct(
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\DataObject\Copy $objectCopyService,
		\Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
		\Magento\Customer\Api\AccountManagementInterface $accountManagement,
		CustomerManagement $customerManagement
	)
	{
		$this->checkoutSession    = $checkoutSession;
		$this->_objectCopyService = $objectCopyService;
		$this->dataObjectHelper   = $dataObjectHelper;
		$this->accountManagement  = $accountManagement;
		$this->customerManagement = $customerManagement;
	}

	/**
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return void
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/** @type \Magento\Quote\Model\Quote $quote */
		$quote = $observer->getEvent()->getQuote();

		/** Validate address */
		$this->validateAddressBeforeSubmit($quote);

		/** One step check out additional data */
		$oscData = $this->checkoutSession->getOscData();

		/** Create account when checkout */
		if (isset($oscData['register']) && $oscData['register']
			&& isset($oscData['password']) && $oscData['password']
		) {
			$quote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER)
				->setCustomerIsGuest(false)
				->setCustomerGroupId(null)
				->setPasswordHash($this->accountManagement->getPasswordHash($oscData['password']));

			$this->_prepareNewCustomerQuote($quote, $oscData);
		}
	}

	/**
	 * Prepare quote for customer registration and customer order submit
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @return void
	 */
	protected function _prepareNewCustomerQuote(Quote $quote, $oscData)
	{
		$billing  = $quote->getBillingAddress();
		$shipping = $quote->isVirtual() ? null : $quote->getShippingAddress();

		$customer  = $quote->getCustomer();
		$dataArray = $billing->getData();
		if (isset($oscData['customerAttributes']) && $oscData['customerAttributes']) {
			$dataArray = array_merge($dataArray, $oscData['customerAttributes']);
		}

		$this->dataObjectHelper->populateWithArray(
			$customer,
			$dataArray,
			'\Magento\Customer\Api\Data\CustomerInterface'
		);

		$quote->setCustomer($customer);

		/** Create customer */
		$this->customerManagement->populateCustomerInfo($quote);

		/** Init customer address */
		$customerBillingData = $billing->exportCustomerAddress();
		$customerBillingData->setIsDefaultBilling(true)
			->setData('should_ignore_validation', true);

		if ($shipping) {
			if (isset($oscData['same_as_shipping']) && $oscData['same_as_shipping']) {
				$shipping->setCustomerAddressData($customerBillingData);
				$customerBillingData->setIsDefaultShipping(true);
			} else {
				$customerShippingData = $shipping->exportCustomerAddress();
				$customerShippingData->setIsDefaultShipping(true)
					->setData('should_ignore_validation', true);
				$shipping->setCustomerAddressData($customerShippingData);
				// Add shipping address to quote since customer Data Object does not hold address information
				$quote->addCustomerAddress($customerShippingData);
			}
		} else {
			$customerBillingData->setIsDefaultShipping(true);
		}
		$billing->setCustomerAddressData($customerBillingData);
		// Add billing address to quote since customer Data Object does not hold address information
		$quote->addCustomerAddress($customerBillingData);

		// If customer is created, set customerId for address to avoid create more address when checkout
		if ($customerId = $quote->getCustomerId()) {
			$billing->setCustomerId($customerId);
			if($shipping) {
				$shipping->setCustomerId($customerId);
			}
		}
	}

	/**
	 * @param \Magento\Quote\Model\Quote $quote
	 * @return $this
	 */
	public function validateAddressBeforeSubmit(\Magento\Quote\Model\Quote $quote)
	{
		/** Remove address validation */
		if (!$quote->isVirtual()) {
			$quote->getShippingAddress()->setShouldIgnoreValidation(true);
		}
		$quote->getBillingAddress()->setShouldIgnoreValidation(true);

		// TODO : Validate address (depend on field require, show on osc or not)

		return $this;
	}
}
