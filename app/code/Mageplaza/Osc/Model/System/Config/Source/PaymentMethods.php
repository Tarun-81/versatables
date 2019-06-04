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
namespace Mageplaza\Osc\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\ScopeInterface;
use Mageplaza\Osc\Helper\Config as OscConfig;

/**
 * Class Methods
 * @package Mageplaza\Osc\Model\System\Config\Source\Payment
 */
class PaymentMethods implements ArrayInterface
{
	/**
	 * @type \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_scopeConfig;

	/**
	 * @type \Magento\Payment\Model\Method\Factory
	 */
	protected $_paymentMethodFactory;

	/**
	 * @type \Mageplaza\Osc\Helper\Config
	 */
	protected $_oscConfig;

	/**
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 * @param \Magento\Payment\Model\Method\Factory $paymentMethodFactory
	 * @param \Mageplaza\Osc\Helper\Config $oscConfig
	 */
	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Payment\Model\Method\Factory $paymentMethodFactory,
		OscConfig $oscConfig
	)
	{
		$this->_scopeConfig          = $scopeConfig;
		$this->_paymentMethodFactory = $paymentMethodFactory;
		$this->_oscConfig            = $oscConfig;
	}

	/**
	 * {@inheritdoc}
	 */
	public function toOptionArray()
	{
		$options = [['label' => __('-- Please select --'), 'value' => '']];

		$payments = $this->getActiveMethods();
		foreach ($payments as $paymentCode => $paymentModel) {
			$options[$paymentCode] = array(
				'label' => $paymentModel->getTitle(),
				'value' => $paymentCode
			);
		}

		return $options;
	}

	/**
	 * Get all active payment method
	 *
	 * @return array
	 */
	public function getActiveMethods()
	{
		$methods       = [];
		$paymentConfig = $this->_scopeConfig->getValue('payment', ScopeInterface::SCOPE_STORE, null);
		if ($this->_oscConfig->isEnabledMultiSafepay()) {
			$paymentConfig = array_merge($this->_scopeConfig->getValue('payment', ScopeInterface::SCOPE_STORE, null), $this->_scopeConfig->getValue('gateways', ScopeInterface::SCOPE_STORE, null));
		}
		foreach ($paymentConfig as $code => $data) {
			if (isset($data['active'], $data['model']) && (bool)$data['active']) {
				try {
					if (class_exists($data['model'])) {
						$methodModel = $this->_paymentMethodFactory->create($data['model']);
						$methodModel->setStore(null);
						if ($methodModel->getConfigData('active', null)) {
							$methods[$code] = $methodModel;
						}
					}

				} catch (\Exception $e) {
					continue;
				}
			}
		}

		return $methods;
	}
}
