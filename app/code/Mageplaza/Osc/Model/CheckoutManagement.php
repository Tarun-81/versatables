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
namespace Mageplaza\Osc\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\GiftMessage\Model\GiftMessageManager;
use Magento\GiftMessage\Model\Message;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Mageplaza\Osc\Api\CheckoutManagementInterface;
use Mageplaza\Osc\Helper\Config as OscConfig;
use Mageplaza\Osc\Model\OscDetailsFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Model\Quote\TotalsCollector;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\Cart\ShippingMethodConverter;

/**
 * Class CheckoutManagement
 * @package Mageplaza\Osc\Model
 */
class CheckoutManagement implements CheckoutManagementInterface
{
	/**
	 * @var CartRepositoryInterface
	 */
	protected $cartRepository;

	/**
	 * @type \Mageplaza\Osc\Model\OscDetailFactory
	 */
	protected $oscDetailsFactory;

	/**
	 * @var \Magento\Quote\Api\ShippingMethodManagementInterface
	 */
	protected $shippingMethodManagement;

	/**
	 * @var \Magento\Quote\Api\PaymentMethodManagementInterface
	 */
	protected $paymentMethodManagement;

	/**
	 * @var \Magento\Quote\Api\CartTotalRepositoryInterface
	 */
	protected $cartTotalsRepository;

	/**
	 * Url Builder
	 *
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlBuilder;

	/**
	 * Checkout session
	 *
	 * @type \Magento\Checkout\Model\Session
	 */
	protected $checkoutSession;

	/**
	 * @var \Magento\Checkout\Api\ShippingInformationManagementInterface
	 */
	protected $shippingInformationManagement;

	/**
	 * @type \Mageplaza\Osc\Helper\Config
	 */
	protected $oscConfig;

	/**
	 * @var Message
	 */
	protected $giftMessage;

	/**
	 * @var GiftMessageManager
	 */
	protected $giftMessageManagement;

	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $_customerSession;

	/**
	 * @var \Magento\Quote\Model\Quote\TotalsCollector
	 */
	protected $_totalsCollector;

	/**
	 * @var \Magento\Quote\Api\Data\AddressInterface
	 */
	protected $_addressInterface;

	/**
	 * @var \Magento\Quote\Model\Cart\ShippingMethodConverter
	 */
	protected $_shippingMethodConverter;


	/**
	 * CheckoutManagement constructor.
	 * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
	 * @param \Mageplaza\Osc\Model\OscDetailsFactory $oscDetailsFactory
	 * @param \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManagement
	 * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
	 * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
	 * @param \Magento\Framework\UrlInterface $urlBuilder
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
	 * @param \Mageplaza\Osc\Helper\Config $oscConfig
	 * @param \Magento\GiftMessage\Model\Message $giftMessage
	 * @param \Magento\GiftMessage\Model\GiftMessageManager $giftMessageManager
	 * @param \Magento\Customer\Model\Session $customerSession
	 */
	public function __construct(
		CartRepositoryInterface $cartRepository,
		OscDetailsFactory $oscDetailsFactory,
		ShippingMethodManagementInterface $shippingMethodManagement,
		PaymentMethodManagementInterface $paymentMethodManagement,
		CartTotalRepositoryInterface $cartTotalsRepository,
		UrlInterface $urlBuilder,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
		OscConfig $oscConfig,
		Message $giftMessage,
		GiftMessageManager $giftMessageManager,
		customerSession $customerSession,
		TotalsCollector $totalsCollector,
		AddressInterface $addressInterface,
		ShippingMethodConverter $shippingMethodConverter
	)
	{
		$this->cartRepository                = $cartRepository;
		$this->oscDetailsFactory             = $oscDetailsFactory;
		$this->shippingMethodManagement      = $shippingMethodManagement;
		$this->paymentMethodManagement       = $paymentMethodManagement;
		$this->cartTotalsRepository          = $cartTotalsRepository;
		$this->_urlBuilder                   = $urlBuilder;
		$this->checkoutSession               = $checkoutSession;
		$this->shippingInformationManagement = $shippingInformationManagement;
		$this->oscConfig                     = $oscConfig;
		$this->giftMessage                   = $giftMessage;
		$this->giftMessageManagement         = $giftMessageManager;
		$this->_customerSession              = $customerSession;
		$this->_totalsCollector              = $totalsCollector;
		$this->_addressInterface             = $addressInterface;
		$this->_shippingMethodConverter      = $shippingMethodConverter;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateItemQty($cartId, $itemId, $itemQty)
	{
		if ($itemQty == 0) {
			return $this->removeItemById($cartId, $itemId);
		}

		/** @var \Magento\Quote\Model\Quote $quote */
		$quote     = $this->cartRepository->getActive($cartId);
		$quoteItem = $quote->getItemById($itemId);
		if (!$quoteItem) {
			throw new NoSuchEntityException(
				__('Cart %1 doesn\'t contain item  %2', $cartId, $itemId)
			);
		}

		try {
			$quoteItem->setQty($itemQty)->save();
			$this->cartRepository->save($quote);
		} catch (\Exception $e) {
			throw new CouldNotSaveException(__('Could not update item from quote'));
		}

		return $this->getResponseData($quote);
	}

	/**
	 * {@inheritDoc}
	 */
	public function removeItemById($cartId, $itemId)
	{
		/** @var \Magento\Quote\Model\Quote $quote */
		$quote     = $this->cartRepository->getActive($cartId);
		$quoteItem = $quote->getItemById($itemId);
		if (!$quoteItem) {
			throw new NoSuchEntityException(
				__('Cart %1 doesn\'t contain item  %2', $cartId, $itemId)
			);
		}
		try {
			$quote->removeItem($itemId);
			$this->cartRepository->save($quote);
		} catch (\Exception $e) {
			throw new CouldNotSaveException(__('Could not remove item from quote'));
		}

		return $this->getResponseData($quote);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPaymentTotalInformation($cartId)
	{
		/** @var \Magento\Quote\Model\Quote $quote */
		$quote = $this->cartRepository->getActive($cartId);

		return $this->getResponseData($quote);
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateGiftWrap($cartId, $isUseGiftWrap)
	{
		/** @var \Magento\Quote\Model\Quote $quote */
		$quote = $this->cartRepository->getActive($cartId);
		$quote->getShippingAddress()->setUsedGiftWrap($isUseGiftWrap);

		try {
			$this->cartRepository->save($quote);
		} catch (\Exception $e) {
			throw new CouldNotSaveException(__('Could not add gift wrap for this quote'));
		}

		return $this->getResponseData($quote);
	}

	/**
	 * Response data to update osc block
	 *
	 * @param \Magento\Quote\Model\Quote $quote
	 * @return \Mageplaza\Osc\Api\Data\OscDetailsInterface
	 */
	public function getResponseData(\Magento\Quote\Model\Quote $quote)
	{
		/** @var \Mageplaza\Osc\Api\Data\OscDetailsInterface $oscDetails */
		$oscDetails = $this->oscDetailsFactory->create();

		if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
			$oscDetails->setRedirectUrl($this->_urlBuilder->getUrl('checkout/cart'));
		} else {
			if ($quote->getShippingAddress()->getCountryId()) {
				$oscDetails->setShippingMethods($this->getShippingMethods($quote));
			}
			$oscDetails->setPaymentMethods($this->paymentMethodManagement->getList($quote->getId()));
			$oscDetails->setTotals($this->cartTotalsRepository->get($quote->getId()));
		}

		return $oscDetails;
	}

	/**
	 * {@inheritDoc}
	 */
	public function saveCheckoutInformation(
		$cartId,
		\Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation,
		$customerAttributes = [],
		$additionInformation = []
	)
	{
		try {
			$additionInformation['customerAttributes'] = $customerAttributes;
			$this->checkoutSession->setOscData($additionInformation);
			$this->addGiftMessage($cartId, $additionInformation);

			if ($addressInformation->getShippingAddress()) {
				if ($this->_customerSession->isLoggedIn() && isset($additionInformation['billing-same-shipping']) && !$additionInformation['billing-same-shipping']) {
					$addressInformation->getShippingAddress()->setSaveInAddressBook(0);
				}
				$this->shippingInformationManagement->saveAddressInformation($cartId, $addressInformation);
			}
		} catch (\Exception $e) {
			throw new InputException(__('Unable to save order information. Please check input data.'));
		}

		return true;
	}

	/**
	 * @param \Magento\Quote\Model\Quote $quote
	 * @return array
	 */
	public function getShippingMethods(\Magento\Quote\Model\Quote $quote)
	{
		$result          = [];
		$shippingAddress = $quote->getShippingAddress();
		$shippingAddress->addData($this->_addressInterface->getData());
		$shippingAddress->setCollectShippingRates(true);
		$this->_totalsCollector->collectAddressTotals($quote, $shippingAddress);
		$shippingRates = $shippingAddress->getGroupedAllShippingRates();
		foreach ($shippingRates as $carrierRates) {
			foreach ($carrierRates as $rate) {
				$result[] = $this->_shippingMethodConverter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
			}
		}

		return $result;
	}

	/**
	 * @param $cartId
	 * @param $additionInformation
	 * @throws \Magento\Framework\Exception\CouldNotSaveException
	 */
	public function addGiftMessage($cartId, $additionInformation)
	{
		/** @var \Magento\Quote\Model\Quote $quote */
		$quote = $this->cartRepository->getActive($cartId);

		if (!$this->oscConfig->isDisabledGiftMessage() && isset($additionInformation['giftMessage'])) {
			$giftMessage = json_decode($additionInformation['giftMessage'], true);
			$this->giftMessage->setSender(isset($giftMessage['sender']) ? $giftMessage['sender'] : '');
			$this->giftMessage->setRecipient(isset($giftMessage['recipient']) ? $giftMessage['recipient'] : '');
			$this->giftMessage->setMessage(isset($giftMessage['message']) ? $giftMessage['message'] : '');
			$this->giftMessageManagement->setMessage($quote, 'quote', $this->giftMessage);
		}
	}
}
