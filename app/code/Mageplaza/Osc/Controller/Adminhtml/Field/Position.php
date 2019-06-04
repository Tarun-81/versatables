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

/**
 * Class Position
 * @package Mageplaza\Osc\Controller\Adminhtml\Field
 */
class Position extends \Magento\Backend\App\Action
{
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}

	/**
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		/**
		 * Set active menu item
		 */
		$resultPage->setActiveMenu('Mageplaza_Osc::field_management');
		$resultPage->getConfig()->getTitle()->prepend(__('Field Management'));

		/**
		 * Add breadcrumb item
		 */
		$resultPage->addBreadcrumb(__('One Step Checkout'), __('One Step Checkout'));
		$resultPage->addBreadcrumb(__('Field Management'), __('Field Management'));

		return $resultPage;
	}
}
