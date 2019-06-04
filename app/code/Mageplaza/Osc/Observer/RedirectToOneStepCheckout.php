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

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Mageplaza\Osc\Helper\Config as HelperConfig;

/**
 * Class RedirectToOneStepCheckout
 * @package Mageplaza\Osc\Observer
 */
class RedirectToOneStepCheckout implements ObserverInterface
{
	/** @var UrlInterface */
	protected $_url;

	/** @var HelperConfig */
	protected $_helperConfig;

	/** @var CheckoutSession */
	protected $checkoutSession;

	/**
	 * RedirectToOneStepCheckout constructor.
	 * @param \Magento\Framework\UrlInterface $url
	 * @param \Mageplaza\Osc\Helper\Config $helperConfig
	 * @param \Magento\Checkout\Model\Session $checkoutSession
	 */
	public function __construct(
		UrlInterface $url,
		HelperConfig $helperConfig,
		CheckoutSession $checkoutSession
	)
	{
		$this->_url            = $url;
		$this->_helperConfig   = $helperConfig;
		$this->checkoutSession = $checkoutSession;
	}

	/**
	 * @param Observer $observer
	 * @return void
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function execute(Observer $observer)
	{
		if ($this->_helperConfig->isEnabled() && $this->_helperConfig->isRedirectToOneStepCheckout()) {
			$request = $observer->getRequest();
			$request->setParam('return_url',$this->_url->getUrl('onestepcheckout/'));
		}
	}
}