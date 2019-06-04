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
namespace Mageplaza\Osc\Controller\Adminhtml\Field;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Mageplaza\Osc\Helper\Config as HelperConfig;

/**
 * Class Save
 * @package Mageplaza\Osc\Controller\Adminhtml\Field
 */
class Save extends \Magento\Backend\App\Action
{
	/**
	 * @var \Magento\Config\Model\ResourceModel\Config
	 */
	protected $resourceConfig;

	/**
	 * Application config
	 *
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $_appConfig;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
	 * @param \Magento\Framework\App\Config\ReinitableConfigInterface $config
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Config\Model\ResourceModel\Config $resourceConfig,
		\Magento\Framework\App\Config\ReinitableConfigInterface $config
	)
	{
		parent::__construct($context);

		$this->resourceConfig = $resourceConfig;
		$this->_appConfig = $config;
	}

	/**
	 * save position to config
	 */
	public function execute()
	{
		$result = [
			'success' => false,
			'message' => __('Error during save field position.')
		];

		$fields = $this->getRequest()->getParam('fields', false);
		if ($fields) {
			$this->resourceConfig->saveConfig(
				HelperConfig::SORTED_FIELD_POSITION,
				$fields,
				ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
				0
			);

			// re-init configuration
			$this->_appConfig->reinit();

			$result['success'] = true;
			$result['message'] = __('All fields have been saved.');
		}

		$this->getResponse()->setBody(\Zend_Json::encode($result));
	}
}
