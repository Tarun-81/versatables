<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use MageWorx\Downloads\Controller\Adminhtml\Attachment as AttachmentController;
use Magento\Framework\Registry;
use MageWorx\Downloads\Model\AttachmentFactory as AttachmentFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\View\Result\LayoutFactory;

class Products extends AttachmentController
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     *
     * @param LayoutFactory $resultLayoutFactory
     * @param Registry $registry
     * @param AttachmentFactory $attachmentFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param Context $context
     */
    public function __construct(
        LayoutFactory $resultLayoutFactory,
        Registry $registry,
        AttachmentFactory $attachmentFactory,
        Context $context
    ) {
    
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($registry, $attachmentFactory, $context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initAttachment();

        $resultLayout = $this->resultLayoutFactory->create();
        /** @var \MageWorx\Downloads\Block\Adminhtml\Attachment\Edit\Tab\Products $productsBlock */
        $productsBlock = $resultLayout->getLayout()->getBlock('attachment_edit_tab_product');
        if ($productsBlock) {
            $productsBlock->setAttachmentProducts($this->getRequest()->getPost('attachment_products', null));
        }
        return $resultLayout;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }
}
