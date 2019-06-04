<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Controller\Adminhtml\Section;

use MageWorx\Downloads\Controller\Adminhtml\Section as SectionController;
use Magento\Framework\Exception\LocalizedException;

class Save extends SectionController
{
    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('section');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $data = $this->filterData($data);
            $section = $this->initSection();
            $section->setData($data);

            $this->_eventManager->dispatch(
                'mageworx_downloads_section_prepare_save',
                [
                    'section' => $section,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $section->getResource()->save($section);
                $this->messageManager->addSuccessMessage(__('The section has been saved.'));
                $this->_getSession()->setMageWorxDownloadsSectionData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageworx_downloads/*/edit',
                        [
                            'section_id' => $section->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageworx_downloads/*/');
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the section.'));
            }

            $this->_getSession()->setMageWorxDownloadsSectionData($data);
            $resultRedirect->setPath(
                'mageworx_downloads/*/edit',
                [
                    'section_id' => $section->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }

        $resultRedirect->setPath('mageworx_downloads/*/');
        return $resultRedirect;
    }
}
