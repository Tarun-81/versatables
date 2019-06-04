<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Attachment;

use MageWorx\Downloads\Controller\Adminhtml\Attachment;

class Delete extends Attachment
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('attachment_id');
        if ($id) {
            $name = "";
            try {
                /** @var \MageWorx\Downloads\Model\Attachment $attachment */
                $attachment = $this->attachmentFactory->create();
                $attachment->getResource()->load($attachment, $id);
                $name = $attachment->getName();
                $attachment->getResource()->delete($attachment);
                $this->messageManager->addSuccessMessage(__('The attachment has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_attchment_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mageworx_downloads/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_attchment_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('mageworx_downloads/*/edit', ['attachment_id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find an attachment to delete.'));
        $resultRedirect->setPath('mageworx_downloads/*/');
        return $resultRedirect;
    }
}
