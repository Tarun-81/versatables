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

/**
 * Class CheckoutSubmitBefore
 * @package Mageplaza\Osc\Observer
 */
class IsAllowedGuestCheckoutObserver extends \Magento\Downloadable\Observer\IsAllowedGuestCheckoutObserver implements ObserverInterface
{
	protected $_helper;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Mageplaza\Osc\Helper\Data $helper
	)
	{
		$this->_helper = $helper;

		parent::__construct($scopeConfig);
	}

	/**
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if ($this->_helper->isEnabled()) {
			return $this;
		}

		return parent::execute($observer);
	}
}
