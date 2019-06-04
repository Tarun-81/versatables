<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use Magento\Backend\App\Action\Context;
use MageWorx\Downloads\Model\AttachmentFactory;
use Magento\Framework\Registry;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Framework\View\Result\PageFactory;

class Edit extends \MageWorx\Downloads\Controller\Adminhtml\Attachment
{
    /**
     * Backend session
     *
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param AttachmentFactory $attachmentFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        PageFactory $resultPageFactory,
        AttachmentFactory $attachmentFactory,
        Context $context
    ) {
    
        $this->backendSession = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($registry, $attachmentFactory, $context);
    }

    /**
     * Is action allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_Downloads::attachments');
    }

    /**
     * Edit attachment page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        
        /** @var \MageWorx\Downloads\Model\Attachment $attachment */
        $attachment = $this->initAttachment();

        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('MageWorx_Downloads::attachments');
        $resultPage->getConfig()->getTitle()->set((__('Attachment')));
        
        if ($this->getRequest()->getParam('attachment_id') && !$attachment->getId()) {
            $this->messageManager->addErrorMessage(__('The attachment no longer exists!'));
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath(
                'mageworx_downloads/*/edit',
                [
                    'attachment_id' => $attachment->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        
        $title = $attachment->getId() ? $attachment->getName() : __('New Attachment');
        $resultPage->getConfig()->getTitle()->append($title);
        $data = $this->backendSession->getData('mageworx_downloads_attachment_data', true);
        if (!empty($data)) {
            $attachment->setData($data);
        }
        
        return $resultPage;
    }
}
