<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml;

use Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData\Collection;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Adminhtml Customer Data Checkout Attribute controller
 *
 */
abstract class CustomerDataCheckoutAttribute extends Action
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * CustomerDataCheckoutAttribute constructor.
     *
     * @param Context $context
     * @param Collection $collection
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Collection $collection,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->collection = $collection;
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Framework\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Sales::sales_order');
        $resultPage->addBreadcrumb(__('Sales'), __('Sales'));
        $resultPage->addBreadcrumb(__('Orders'), __('Orders'));

        return $resultPage;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function getResult()
    {
        $orderId = $this->getRequest()->getParam('orderid');
        $checkoutFieldsData = $this->collection->getAitocCheckoutfieldsByOrderId((int)$orderId, true);
        $this->coreRegistry->register('checkout_fields_data', $checkoutFieldsData);
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Aitoc_CheckoutFieldsManager::attributes');
    }
}
