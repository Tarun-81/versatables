<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Catalog\Product;

use Magento\Catalog\Controller\Adminhtml\Product\Edit;

class Attachments extends Edit
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
    
        parent::__construct($context, $productBuilder, $resultPageFactory);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Layout|$this
     */
    public function execute()
    {
        $productId = (int) $this->getRequest()->getParam('id');
        $product = $this->productBuilder->build($this->getRequest());

        if ($productId && !$product->getId()) {
            $this->messageManager->addErrorMessage(__('This product no longer exists.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('catalog/*/');
        }
        $resultLayout = $this->resultLayoutFactory->create();
        /** @var \MageWorx\Downloads\Block\Adminhtml\Catalog\Product\Edit\Tab\Attachment $attachmentBlock */


        $attachmentBlock = $resultLayout->getLayout()->getBlock('mageworx_downloads.attachment');
        if ($attachmentBlock) {
            $attachmentBlock->setProductAttachments($this->getRequest()->getPost('product_attachments', null));
        }

        return $resultLayout;
    }
}
