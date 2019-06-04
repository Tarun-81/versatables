<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use MageWorx\Downloads\Controller\Adminhtml\Section;

class Delete extends Section
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('section_id');
        if ($id) {
            $name = "";
            try {
                /** @var \MageWorx\Downloads\Model\Section $section */
                $section = $this->sectionFactory->create();
                //$section->load($id);
                $section->getResource()->load($section, $id);
                $name = $section->getName();
                $section->delete();
                $this->messageManager->addSuccessMessage(__('The section has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_section_on_delete',
                    ['name' => $name, 'status' => 'success']
                );
                $resultRedirect->setPath('mageworx_downloads/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_mageworx_downloads_section_on_delete',
                    ['name' => $name, 'status' => 'fail']
                );
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath('mageworx_downloads/*/edit', ['section_id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a section to delete.'));
        $resultRedirect->setPath('mageworx_downloads/*/');
        return $resultRedirect;
    }
}
